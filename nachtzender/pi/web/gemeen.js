// Gedeelde code voor alle pagina's van de Nachtzender.
const NZ = (() => {
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const wacht = ms => new Promise(r => setTimeout(r, ms));

  async function post(url, data) {
    const res = await fetch(url, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data || {}) });
    return res.json();
  }
  const status = async () => (await fetch("/api/status")).json();

  // ---- navigatie bovenaan ----
  const PAGINAS = [
    ["/missie", "🗺️", "Missie"], ["/netwerken", "1", "Netwerken"], ["/embedded", "2", "Embedded"],
    ["/linux", "3", "Linux"], ["/database", "4", "Database"], ["/ontcijferen", "5", "Ontcijferen"],
    ["/kluis", "🔐", "Kluis"],
  ];

  function nav(actief) {
    const el = document.createElement("nav");
    el.className = "nav";
    el.innerHTML = `<div class="nav-binnen"><a class="nav-logo" href="/missie">📻 NACHTZENDER</a>
      ${PAGINAS.map(([href, nr, naam]) => `<a class="post${href === actief ? " actief" : ""}" href="${href}" data-post="${nr}"
        ${href === actief ? 'aria-current="page"' : ""}><span class="nr">${nr}</span>${naam}</a>`).join("")}
      <span class="nav-status" id="nav-status"></span></div>`;
    document.body.prepend(el);
    const ververs = async () => {
      try {
        const d = await status();
        const r = d.ronde;
        for (const a of el.querySelectorAll("a.post")) {
          a.classList.toggle("gevonden", !!r && r.gevonden.includes(Number(a.dataset.post)));
        }
        el.querySelector("#nav-status").innerHTML = !r ? "Geen missie actief"
          : r.opgelost ? `✅ ${esc(r.team)}: missie geslaagd` : `<span class="live">LIVE</span> ${esc(r.team)}`;
      } catch (e) { /* zender even weg */ }
    };
    ververs();
    setInterval(ververs, 3000);
  }

  // ---- vakje om een gevonden sleutelgroep na te laten kijken ----
  function sleutelvak(el, postNr) {
    el.innerHTML = `<div class="sleutelvak">
      <label for="sv${postNr}"><b>Gevonden sleutelgroep ${postNr}:</b></label>
      <input id="sv${postNr}" inputmode="numeric" autocomplete="off" maxlength="5" placeholder="•••••">
      <button class="hoofd">Controleer</button><span class="melding" role="status"></span></div>`;
    const invoer = el.querySelector("input"), melding = el.querySelector(".melding");
    const controleer = async () => {
      const d = await post("/api/controleer", { post: postNr, waarde: invoer.value });
      melding.className = "melding " + (d.goed ? "goed" : "fout");
      melding.textContent = d.goed ? "✔ Klopt! Schrijf hem op jullie missiekaart."
        : invoer.value.replace(/\D/g, "").length !== 5 ? "Een sleutelgroep heeft 5 cijfers." : "✖ Dat is hem niet. Kijk nog eens goed.";
    };
    el.querySelector("button").onclick = controleer;
    invoer.onkeydown = e => e.key === "Enter" && controleer();
  }

  // ---- slepen: werkt met muis, touchscreen én door te klikken ----
  // Alle .tegel-elementen binnen root zijn sleepbaar, alle .vak-elementen zijn landingsplekken.
  // opNeerzetten(tegel, vak) beslist wat er gebeurt.
  function sleepbaar(root, opNeerzetten) {
    let gekozen = null;
    const kies = tegel => {
      gekozen?.classList.remove("gekozen");
      gekozen = gekozen === tegel ? null : tegel;
      gekozen?.classList.add("gekozen");
    };
    root.addEventListener("pointerdown", e => {
      const tegel = e.target.closest(".tegel");
      if (!tegel || !root.contains(tegel) || e.button > 0) return;
      e.preventDefault();
      const start = { x: e.clientX, y: e.clientY };
      let geest = null, over = null;
      const beweeg = ev => {
        if (!geest && Math.hypot(ev.clientX - start.x, ev.clientY - start.y) < 6) return;
        if (!geest) {
          geest = tegel.cloneNode(true);
          geest.classList.add("geest");
          geest.classList.remove("gekozen");
          document.body.append(geest);
          tegel.classList.add("wordt-gesleept");
        }
        geest.style.left = ev.clientX + "px";
        geest.style.top = ev.clientY + "px";
        const vak = document.elementFromPoint(ev.clientX, ev.clientY)?.closest(".vak");
        const geldig = vak && root.contains(vak) ? vak : null;
        if (over !== geldig) { over?.classList.remove("over"); geldig?.classList.add("over"); over = geldig; }
      };
      const los = () => {
        removeEventListener("pointermove", beweeg);
        removeEventListener("pointerup", los);
        removeEventListener("pointercancel", los);
        over?.classList.remove("over");
        tegel.classList.remove("wordt-gesleept");
        if (geest) {
          geest.remove();
          if (over) { gekozen?.classList.remove("gekozen"); gekozen = null; opNeerzetten(tegel, over); }
        } else {
          kies(tegel);
        }
      };
      addEventListener("pointermove", beweeg);
      addEventListener("pointerup", los);
      addEventListener("pointercancel", los);
    });
    root.addEventListener("click", e => {
      const tegel = e.target.closest(".tegel");
      if (tegel && e.detail === 0) { kies(tegel); return; }  // toetsenbord (Enter/spatie)
      const vak = e.target.closest(".vak");
      if (vak && gekozen && !tegel) {
        const t = gekozen;
        kies(t);
        opNeerzetten(t, vak);
      }
    });
  }

  // Standaard: verplaats de tegel naar het vak. Een vak met data-max="1" houdt één tegel;
  // de vorige gaat terug naar zijn eigen bak (data-thuis).
  function verplaats(tegel, vak) {
    if (vak.dataset.max === "1") {
      for (const oud of vak.querySelectorAll(".tegel")) {
        if (oud !== tegel) document.getElementById(oud.dataset.thuis)?.append(oud);
      }
    }
    vak.append(tegel);
  }

  // ---- de stem van de zender in de browser ----
  const MELODIE = [[659, .25], [587, .25], [523, .5], [0, .1], [659, .25], [587, .25], [523, .5],
                   [0, .1], [392, .25], [440, .25], [494, .25], [523, .75]];  // zelfde als omroeper.py
  const zender = (() => {
    const kanPraten = "speechSynthesis" in window;
    let aan = false, bezig = false, bezigMet = null, laatste = null, ctx = null, knop = null, melding = null;

    const nogActief = n => aan && laatste?.ronde && !laatste.ronde.opgelost && laatste.ronde.nummer === n;
    const wachtZolang = async (ms, n) => { for (let t = 0; t < ms && nogActief(n); t += 100) await wacht(100); };

    function kiesStem(taal) {
      if (!kanPraten) return null;
      const prefix = taal.slice(0, 2);
      const stemmen = speechSynthesis.getVoices().filter(v => v.lang.toLowerCase().startsWith(prefix));
      return stemmen.find(v => /natural|online|google/i.test(v.name)) || stemmen[0] || null;
    }
    function toonMelding() {
      if (!melding || !laatste) return;
      const taal = laatste.taal || "nl-NL";
      const ontbreekt = aan && kanPraten && speechSynthesis.getVoices().length && !kiesStem(taal);
      melding.hidden = !ontbreekt;
      melding.textContent = `Deze browser heeft geen stem voor ${taal}. Gebruik Microsoft Edge (heeft de meeste stemmen) `
        + "of voeg de taal toe in Windows: Instellingen > Tijd en taal > Spraak.";
    }
    if (kanPraten) speechSynthesis.onvoiceschanged = toonMelding;

    async function speelMelodie(n) {
      const uit = ctx.createGain();
      uit.gain.value = 0.25;
      uit.connect(ctx.destination);
      let t = ctx.currentTime + 0.05;
      for (const [freq, duur] of MELODIE) {
        if (freq) {
          const osc = ctx.createOscillator(), g = ctx.createGain();
          osc.frequency.value = freq;
          g.gain.setValueAtTime(0, t);
          g.gain.linearRampToValueAtTime(1, t + 0.02);
          g.gain.setValueAtTime(1, t + duur - 0.03);
          g.gain.linearRampToValueAtTime(0, t + duur);
          osc.connect(g).connect(uit);
          osc.start(t); osc.stop(t + duur);
        }
        t += duur;
      }
      await wachtZolang((t - ctx.currentTime) * 1000, n);
      uit.disconnect();  // stopt de melodie meteen als de ronde wisselt
    }

    function zeg(zin, taal) {
      return new Promise(klaar => {
        const u = new SpeechSynthesisUtterance(zin);
        u.lang = taal; u.rate = 0.75; u.pitch = 0.5;
        const stem = kiesStem(taal);
        if (stem) u.voice = stem;
        u.onend = u.onerror = klaar;
        speechSynthesis.speak(u);
      });
    }

    async function uitzenden(n, zinnen, taal) {
      bezig = true; bezigMet = n;
      try {
        await speelMelodie(n);
        for (const zin of zinnen) {
          if (!nogActief(n)) break;
          if (!zin) { await wacht(700); continue; }
          if (kanPraten) await zeg(zin, taal); else await wacht(600);
          await wacht(150);
        }
        await wachtZolang(8000, n);
      } finally {
        if (kanPraten) speechSynthesis.cancel();
        bezig = false;
      }
    }

    return {
      koppel(knopEl, meldingEl) {
        knop = knopEl; melding = meldingEl;
        knop.onclick = () => {
          aan = !aan;
          ctx = ctx || new AudioContext();
          ctx.resume();
          knop.textContent = aan ? "🔊 Geluid uit" : "🔇 Geluid aan";
          knop.setAttribute("aria-pressed", aan);
          if (!aan && kanPraten) speechSynthesis.cancel();
          toonMelding();
          if (laatste) this.update(laatste);
        };
      },
      update(d) {
        laatste = d;
        if (bezig && !nogActief(bezigMet) && kanPraten) speechSynthesis.cancel();
        const r = d.ronde;
        if (aan && r && !r.opgelost && !bezig) uitzenden(r.nummer, d.uitzending, d.taal);
        toonMelding();
      },
    };
  })();

  const mmss = s => String(Math.floor(s / 60)).padStart(2, "0") + ":" + String(Math.floor(s % 60)).padStart(2, "0");

  return { esc, wacht, post, status, nav, sleutelvak, sleepbaar, verplaats, zender, mmss };
})();
