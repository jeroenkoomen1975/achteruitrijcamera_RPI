// ICT Quiz Battle - het scherm. Alle spelregels zitten op de server;
// dit script laat alleen zien wat de server stuurt.
"use strict";

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

let ws = null;
let toestand = null;
let ontvangenOp = 0;      // performance.now() bij de laatste toestand
let vorige = null;         // vorige toestand, om veranderingen te zien
let geluidAan = true;
let laatsteTik = null;
let getoondeVraag = -1;

// ---------------------------------------------------------------- verbinding

function verbind() {
  ws = new WebSocket(`${location.protocol === "https:" ? "wss" : "ws"}://${location.host}/ws`);
  ws.onopen = () => $("#geen-verbinding").classList.add("verborgen");
  ws.onmessage = (e) => {
    vorige = toestand;
    toestand = JSON.parse(e.data);
    ontvangenOp = performance.now();
    render();
  };
  ws.onclose = () => {
    $("#geen-verbinding").classList.remove("verborgen");
    setTimeout(verbind, 1000);
  };
}

function stuur(bericht) {
  if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify(bericht));
}

// Resterende tijd in ms, lokaal bijgewerkt tussen twee berichten van de server in.
function resterend() {
  if (!toestand || toestand.resterend_ms == null) return null;
  return Math.max(0, toestand.resterend_ms - (performance.now() - ontvangenOp));
}

// ---------------------------------------------------------------- tekenen

function toonScherm(naam) {
  for (const s of $$(".scherm")) s.classList.toggle("actief", s.id === `scherm-${naam}`);
}

function render() {
  const t = toestand;
  const faseVeranderd = !vorige || vorige.fase !== t.fase;
  if (t.fase !== "vraag" && t.fase !== "uitslag") getoondeVraag = -1;

  renderRecords(t);

  switch (t.fase) {
    case "lobby":    renderLobby(t); toonScherm("lobby"); break;
    case "aftellen": toonScherm("aftellen"); break;
    case "vraag":
    case "uitslag":  renderVraag(t); toonScherm("vraag"); break;
    case "einde":    renderEinde(t); toonScherm("einde"); break;
  }

  if (faseVeranderd) {
    if (t.fase === "vraag") geluid.nieuweVraag();
    if (t.fase === "uitslag") {
      const goed = t.spelers.some((s) => s.meedoen && s.antwoord === t.vraag.goed);
      goed ? geluid.goed() : geluid.fout();
    }
    if (t.fase === "einde") { geluid.fanfare(); confetti.start(); }
    if (t.fase === "uitslag" && snelheidsrecordNu(t)) geluid.record();
    if (t.fase !== "einde") confetti.stop();
  }

  // Een speler heeft net een antwoord vastgezet
  if (vorige && t.fase === "vraag" && vorige.fase === "vraag") {
    for (const s of t.spelers) {
      const was = vorige.spelers.find((v) => v.nummer === s.nummer);
      if (s.heeft_geantwoord && !was.heeft_geantwoord) geluid.vast();
    }
  }
  // Iemand meldt zich in de lobby
  if (vorige && t.fase === "lobby" && vorige.fase === "lobby") {
    for (const s of t.spelers) {
      const was = vorige.spelers.find((v) => v.nummer === s.nummer);
      if (s.klaar && !was.klaar) geluid.meedoen();
    }
  }
}

// Heeft iemand bij deze vraag net het snelheidsrecord verbroken?
function snelheidsrecordNu(t) {
  const rec = t.records.snelste_antwoord;
  return t.fase === "uitslag" && t.nieuwe_records.snelste && rec != null &&
    t.spelers.some((s) => s.punten > 0 && s.antwoord_tijd === rec.tijd);
}

function renderRecords(t) {
  const { hoogste_score: score, snelste_antwoord: snelste } = t.records;
  const wie = (nr) => `<span class="wie" data-speler="${nr}">speler ${nr}</span>`;
  $(".record-waarde", $("#record-score")).innerHTML = score ? `${score.score}${wie(score.speler)}` : "—";
  $(".record-waarde", $("#record-snelste")).innerHTML =
    snelste ? `${snelste.tijd.toFixed(2).replace(".", ",")} s${wie(snelste.speler)}` : "—";

  const nieuwScore = t.fase === "einde" && t.nieuwe_records.score;
  const nieuwSnelste = snelheidsrecordNu(t) || (t.fase === "einde" && t.nieuwe_records.snelste);
  $("#record-score").classList.toggle("nieuw", nieuwScore);
  $("#record-snelste").classList.toggle("nieuw", nieuwSnelste);
  $("#records").classList.toggle("feest", nieuwScore || nieuwSnelste);
}

function renderLobby(t) {
  const chips = $("#lobby-chips");
  if (!chips.children.length) {
    t.categorieen.forEach(([naam, icoon], i) => {
      const chip = document.createElement("div");
      chip.className = "chip";
      chip.style.animationDelay = `${-i * 0.5}s`;
      chip.textContent = `${icoon} ${naam}`;
      chips.append(chip);
    });
  }
  for (const s of t.spelers) {
    const kaart = $(`.lobby-speler[data-speler="${s.nummer}"]`);
    kaart.classList.toggle("klaar", s.klaar);
    $(".lobby-status", kaart).textContent = s.klaar ? "✔ Klaar om te spelen!" : "Druk op een knop!";
    const kast = $(".kast-status", kaart);
    kast.classList.toggle("aan", s.verbonden);
    kast.textContent = s.verbonden ? "● Knoppen verbonden" : "○ Knoppen niet verbonden";
  }
}

function renderVraag(t) {
  const v = t.vraag;
  const scherm = $("#scherm-vraag");
  const onthuld = t.fase === "uitslag";

  if (getoondeVraag !== t.vraag_nr) {
    getoondeVraag = t.vraag_nr;
    laatsteTik = null;
    $(".icoon", $("#categorie")).textContent = v.icoon;
    $(".naam", $("#categorie")).textContent = v.categorie_naam;
    $("#vraag-nr").textContent = `Vraag ${t.vraag_nr} van ${t.aantal_vragen}`;
    const vraagTekst = $("#vraag-tekst");
    vraagTekst.textContent = v.tekst;
    vraagTekst.classList.toggle("lang", v.tekst.length > 70);
    $$(".antwoord").forEach((el, i) => {
      const tekst = $(".tekst", el);
      tekst.textContent = v.antwoorden[i];
      tekst.classList.toggle("lang", v.antwoorden[i].length > 34);
      el.classList.remove("goed");
      $(".kiezers", el).replaceChildren();
    });
    // binnenkomst-animatie opnieuw starten
    scherm.classList.remove("nieuw");
    void scherm.offsetWidth;
    scherm.classList.add("nieuw");

    const voortgang = $("#voortgang");
    voortgang.replaceChildren(...Array.from({ length: t.aantal_vragen }, (_, i) => {
      const bol = document.createElement("span");
      if (i < t.vraag_nr - 1) bol.className = "gedaan";
      if (i === t.vraag_nr - 1) bol.className = "nu";
      return bol;
    }));
  }

  $("#antwoorden").classList.toggle("onthuld", onthuld);
  scherm.classList.toggle("onthuld", onthuld);
  $("#timer").classList.toggle("uit", onthuld);
  const uitleg = $("#uitleg");
  uitleg.textContent = onthuld && v.uitleg ? v.uitleg : "";
  uitleg.classList.toggle("zichtbaar", onthuld && !!v.uitleg);
  pasVraagInKaart();

  if (onthuld) {
    $$(".antwoord").forEach((el, i) => {
      el.classList.toggle("goed", i === v.goed);
      const kiezers = $(".kiezers", el);
      if (kiezers.children.length) return;
      for (const s of t.spelers) {
        if (!s.meedoen || s.antwoord !== i) continue;
        const badge = document.createElement("div");
        badge.className = "kiezer";
        badge.dataset.speler = s.nummer;
        badge.textContent = s.nummer;
        kiezers.append(badge);
      }
    });
  }

  for (const s of t.spelers) {
    const kaart = $(`.speler-kaart[data-speler="${s.nummer}"]`);
    const status = $(".status", kaart);
    kaart.classList.toggle("uit", !s.meedoen);
    kaart.classList.toggle("vast", !onthuld && s.heeft_geantwoord);
    kaart.classList.toggle("goed", onthuld && s.punten > 0);
    kaart.classList.toggle("fout", onthuld && s.meedoen && s.punten === 0);

    if (!s.meedoen) status.textContent = "Doet niet mee";
    else if (!onthuld) status.textContent = s.heeft_geantwoord ? "🔒 Antwoord vastgezet" : "Denkt na…";
    else if (s.antwoord == null) status.textContent = "⏰ Te laat!";
    else if (s.punten > 0) status.textContent = `✔ Goed! in ${s.antwoord_tijd.toFixed(1)} s`;
    else status.textContent = "✘ Helaas, fout";

    telOp($(".score", kaart), s.score);
    const pop = $(".punten-pop", kaart);
    if (onthuld && s.punten > 0 && (!vorige || vorige.fase !== "uitslag")) {
      pop.textContent = `+${s.punten}`;
      pop.classList.remove("toon");
      void pop.offsetWidth;
      pop.classList.add("toon");
    }
  }
}

function renderEinde(t) {
  const titel = $("#einde-titel");
  const deelnemers = t.spelers.filter((s) => s.meedoen);
  if (t.winnaar === null) {
    const s = deelnemers[0];
    titel.innerHTML = `Goed gespeeld, <span class="naam-kleur" data-speler="${s.nummer}">Speler ${s.nummer}</span>!`;
  } else if (t.winnaar === 0) {
    titel.textContent = "Gelijkspel! 🤝";
  } else {
    titel.innerHTML = `<span class="naam-kleur" data-speler="${t.winnaar}">Speler ${t.winnaar}</span> wint!`;
  }

  const meldingen = [];
  if (t.nieuwe_records.score) meldingen.push("⭐ Nieuwe highscore!");
  if (t.nieuwe_records.snelste) meldingen.push("⚡ Nieuw snelheidsrecord!");
  const banner = $("#nieuw-record");
  banner.textContent = meldingen.join("   ");
  banner.classList.toggle("verborgen", meldingen.length === 0);

  for (const s of t.spelers) {
    const kaart = $(`.einde-speler[data-speler="${s.nummer}"]`);
    kaart.classList.toggle("verborgen", !s.meedoen);
    kaart.classList.toggle("winnaar", t.winnaar === s.nummer);
    kaart.classList.toggle("verloren", t.winnaar > 0 && t.winnaar !== s.nummer);
    telOp($(".einde-score", kaart), s.score, 1500);
    $(".einde-detail", kaart).textContent = `${s.aantal_goed} van de ${t.aantal_vragen} vragen goed`;
  }
}

// Maak de vraagtekst kleiner tot alles in de kaart past (lange vragen, of vraag + uitleg).
function pasVraagInKaart() {
  const kaart = $(".vraag-kaart");
  const tekst = $("#vraag-tekst");
  tekst.style.fontSize = "";
  const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
  let grootte = parseFloat(getComputedStyle(tekst).fontSize);
  while (kaart.scrollHeight > kaart.clientHeight + 1 && grootte > 1.6 * rem) {
    grootte -= 0.1 * rem;
    tekst.style.fontSize = `${grootte}px`;
  }
}

// Laat een getal soepel optellen naar de nieuwe waarde.
function telOp(el, naar, duur = 900) {
  const van = Number(el.dataset.waarde || 0);
  if (van === naar) { el.textContent = naar; return; }
  el.dataset.waarde = naar;
  const start = performance.now();
  const stap = (nu) => {
    const f = Math.min(1, (nu - start) / duur);
    const ease = 1 - Math.pow(1 - f, 3);
    el.textContent = Math.round(van + (naar - van) * ease);
    if (f < 1 && Number(el.dataset.waarde) === naar) requestAnimationFrame(stap);
  };
  requestAnimationFrame(stap);
}

// ---------------------------------------------------------------- per frame

function frame() {
  requestAnimationFrame(frame);
  if (!toestand) return;
  const rest = resterend();

  const balk = $("#tijdbalk");
  balk.classList.toggle("uit", toestand.fase !== "vraag");
  if (toestand.fase === "vraag" && rest != null) {
    const deel = rest / toestand.duur_ms;  // 1 = net begonnen, 0 = tijd op
    const vulling = $(".tijdbalk-vulling", balk);
    vulling.style.transform = `scaleX(${deel})`;
    vulling.style.setProperty("--balk-kleur", `hsl(${Math.round(deel * 130)}, 85%, 52%)`);
    balk.classList.toggle("bijna", rest <= 3000);
  }

  if (toestand.fase === "vraag" && rest != null) {
    const sec = Math.ceil(rest / 1000);
    $(".timer-getal").textContent = sec;
    $(".timer-ring").style.strokeDashoffset = 100 - (rest / toestand.duur_ms) * 100;
    $("#timer").classList.toggle("bijna", sec <= 5);
    if (sec <= 5 && sec > 0 && sec !== laatsteTik) { laatsteTik = sec; geluid.tik(); }
  }

  if (toestand.fase === "aftellen" && rest != null) {
    const sec = Math.max(1, Math.ceil(rest / 1000));
    const el = $("#aftel-getal");
    if (el.textContent !== String(sec)) {
      el.textContent = sec;
      el.classList.remove("tik");
      void el.offsetWidth;
      el.classList.add("tik");
      geluid.aftel();
    }
  }

  if (toestand.fase === "lobby") {
    const hint = $("#lobby-hint");
    const klaar = toestand.spelers.filter((s) => s.klaar);
    if (klaar.length === 1 && rest != null) {
      const ander = toestand.spelers.find((s) => !s.klaar).nummer;
      hint.innerHTML = `Wachten op speler ${ander}… alleen spelen start over <b>${Math.ceil(rest / 1000)}</b> s`;
    } else {
      hint.textContent = "Druk op een van je knoppen om mee te doen";
    }
  }

  if (toestand.fase === "einde") {
    const hint = $("#einde-hint");
    const voorbij = toestand.duur_ms - (rest ?? 0);
    const kanDrukken = voorbij > 4000;
    hint.classList.toggle("knipper", kanDrukken);
    hint.textContent = kanDrukken ? "Druk op een knop om opnieuw te spelen" : "";
  }
}

// ---------------------------------------------------------------- geluid (Web Audio, geen bestanden nodig)

const geluid = (() => {
  let ctx = null;
  function toon(freq, start, duur, type = "sine", volume = 0.18) {
    if (!geluidAan) return;
    try {
      ctx = ctx || new AudioContext();
      if (ctx.state === "suspended") ctx.resume();
      const t0 = ctx.currentTime + start;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = type;
      osc.frequency.setValueAtTime(freq, t0);
      gain.gain.setValueAtTime(0, t0);
      gain.gain.linearRampToValueAtTime(volume, t0 + 0.01);
      gain.gain.exponentialRampToValueAtTime(0.001, t0 + duur);
      osc.connect(gain).connect(ctx.destination);
      osc.start(t0);
      osc.stop(t0 + duur + 0.05);
    } catch (e) { /* geen geluid beschikbaar */ }
  }
  return {
    tik:        () => toon(880, 0, 0.08, "square", 0.06),
    aftel:      () => toon(660, 0, 0.25, "triangle"),
    vast:       () => toon(520, 0, 0.1, "triangle", 0.12),
    meedoen:    () => { toon(523, 0, 0.12, "triangle"); toon(784, 0.1, 0.2, "triangle"); },
    nieuweVraag: () => { toon(392, 0, 0.12); toon(587, 0.08, 0.2); },
    goed:       () => { toon(523, 0, 0.15, "triangle"); toon(659, 0.12, 0.15, "triangle"); toon(784, 0.24, 0.35, "triangle"); },
    fout:       () => { toon(220, 0, 0.25, "sawtooth", 0.08); toon(165, 0.2, 0.4, "sawtooth", 0.08); },
    record:     () => [784, 988, 1175, 1568].forEach((f, i) => toon(f, 0.5 + i * 0.09, 0.25, "square", 0.07)),
    fanfare:    () => [523, 659, 784, 1047, 784, 1047].forEach((f, i) => toon(f, i * 0.14, i === 5 ? 0.8 : 0.2, "triangle")),
  };
})();

// ---------------------------------------------------------------- confetti

const confetti = (() => {
  const canvas = $("#confetti");
  const c = canvas.getContext("2d");
  const kleuren = ["#e5484d", "#3e7bfa", "#23a861", "#f5b30b", "#ff4fa3", "#22d3ee", "#ffffff"];
  let deeltjes = [];
  let actief = false;
  let stopOp = 0;

  function maak() {
    return {
      x: Math.random() * canvas.width,
      y: -20 - Math.random() * canvas.height * 0.5,
      w: 8 + Math.random() * 10,
      h: 10 + Math.random() * 14,
      vy: 2 + Math.random() * 4,
      vx: -1.5 + Math.random() * 3,
      hoek: Math.random() * Math.PI,
      draai: -0.15 + Math.random() * 0.3,
      kleur: kleuren[Math.floor(Math.random() * kleuren.length)],
    };
  }
  function stap() {
    if (!actief) { c.clearRect(0, 0, canvas.width, canvas.height); return; }
    c.clearRect(0, 0, canvas.width, canvas.height);
    const doorgaan = performance.now() < stopOp;
    deeltjes = deeltjes.filter((d) => d.y < canvas.height + 30 || doorgaan);
    for (const d of deeltjes) {
      if (d.y > canvas.height + 30 && doorgaan) Object.assign(d, maak(), { y: -20 });
      d.x += d.vx; d.y += d.vy; d.hoek += d.draai;
      c.save();
      c.translate(d.x, d.y);
      c.rotate(d.hoek);
      c.fillStyle = d.kleur;
      c.fillRect(-d.w / 2, -d.h / 2, d.w, d.h * Math.abs(Math.cos(d.hoek * 2)));
      c.restore();
    }
    if (deeltjes.length) requestAnimationFrame(stap);
    else actief = false;
  }
  return {
    start() {
      canvas.width = innerWidth; canvas.height = innerHeight;
      deeltjes = Array.from({ length: 220 }, maak);
      stopOp = performance.now() + 7000;
      if (!actief) { actief = true; requestAnimationFrame(stap); }
    },
    stop() { actief = false; },
  };
})();

// ---------------------------------------------------------------- toetsenbord (reserve voor de knoppen)

const TOETSEN = {
  "1": [1, 0], "2": [1, 1], "3": [1, 2], "4": [1, 3],
  "7": [2, 0], "8": [2, 1], "9": [2, 2], "0": [2, 3],
};

addEventListener("keydown", (e) => {
  if (e.repeat) return;
  const druk = TOETSEN[e.key];
  if (druk) stuur({ type: "druk", speler: druk[0], knop: druk[1] });
  else if (e.key === "Escape") stuur({ type: "reset" });
  else if (e.key === "f" || e.key === "F") {
    document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen();
  } else if (e.key === "m" || e.key === "M") geluidAan = !geluidAan;
  else if (e.key === "h" || e.key === "H") $("#toetsen-hulp").classList.toggle("verborgen");
});

addEventListener("resize", () => { if (getoondeVraag > 0) pasVraagInKaart(); });

verbind();
requestAnimationFrame(frame);
