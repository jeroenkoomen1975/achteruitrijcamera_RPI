"use strict";

const KOLOMMEN = [
  { id: "backlog", naam: "Product backlog" },
  { id: "todo", naam: "Sprint backlog" },
  { id: "bezig", naam: "Bezig" },
  { id: "review", naam: "Review / test" },
  { id: "klaar", naam: "Klaar" },
];
const SPRINT_KOLOMMEN = ["todo", "bezig", "review", "klaar"];
const VERVERS_MS = 4000;

const $ = (sel) => document.querySelector(sel);

let groep = null;      // { id, naam, token }
let bord = null;       // laatste antwoord van de server
let bewerkKaart = null;
let gesleept = null;

// ---------------------------------------------------------------- opslag token

function bewaarToken(id, token) {
  try { localStorage.setItem(`scrum-token-${id}`, token); } catch {}
}
function leesToken(id) {
  try { return localStorage.getItem(`scrum-token-${id}`); } catch { return null; }
}
function vergeetToken(id) {
  try { localStorage.removeItem(`scrum-token-${id}`); } catch {}
}

// ---------------------------------------------------------------- server

async function api(methode, pad, gegevens) {
  const opties = { method: methode, headers: {} };
  if (groep) opties.headers["X-Token"] = groep.token;
  if (gegevens !== undefined) {
    opties.headers["Content-Type"] = "application/json";
    opties.body = JSON.stringify(gegevens);
  }
  const antwoord = await fetch(pad, opties);
  const inhoud = await antwoord.json().catch(() => ({}));
  if (!antwoord.ok) {
    const fout = new Error(inhoud.fout || `fout ${antwoord.status}`);
    fout.status = antwoord.status;
    throw fout;
  }
  return inhoud;
}

const groepPad = (rest = "") => `/api/groepen/${groep.id}${rest}`;

// ---------------------------------------------------------------- startscherm

async function toonStart() {
  groep = null;
  bord = null;
  history.replaceState(null, "", "#");
  $("#bord-scherm").hidden = true;
  $("#start").hidden = false;
  document.title = "Scrumboard";
  const lijst = $("#groepen");
  lijst.replaceChildren();
  const groepen = await api("GET", "/api/groepen");
  $("#geen-groepen").hidden = groepen.length > 0;
  for (const g of groepen) {
    const li = document.createElement("li");
    const knop = document.createElement("button");
    knop.textContent = g.naam;
    knop.addEventListener("click", () => openGroep(g));
    li.append(knop);
    lijst.append(li);
  }
}

async function openGroep(g) {
  const token = leesToken(g.id);
  if (token) {
    groep = { id: g.id, naam: g.naam, token };
    try {
      await laadBord();
      return toonBord();
    } catch (fout) {
      if (fout.status !== 403) throw fout;
      vergeetToken(g.id);
    }
  }
  vraagPin(g);
}

function vraagPin(g) {
  const dialoog = $("#pin-dialoog");
  const form = dialoog.querySelector("form");
  form.reset();
  form.querySelector(".fout").textContent = "";
  $("#pin-groep").textContent = g.naam;
  dialoog.onclose = null;
  form.onsubmit = async (e) => {
    if (e.submitter?.value !== "ok") return;
    e.preventDefault();
    try {
      const antwoord = await api("POST", `/api/groepen/${g.id}/login`, { pin: form.pin.value });
      bewaarToken(antwoord.id, antwoord.token);
      groep = antwoord;
      dialoog.close();
      await laadBord();
      toonBord();
    } catch (fout) {
      form.querySelector(".fout").textContent = fout.message;
    }
  };
  dialoog.showModal();
}

$("#nieuwe-groep").addEventListener("submit", async (e) => {
  e.preventDefault();
  const form = e.target;
  const foutVak = form.querySelector(".fout");
  foutVak.textContent = "";
  try {
    const antwoord = await api("POST", "/api/groepen", { naam: form.naam.value, pin: form.pin.value });
    bewaarToken(antwoord.id, antwoord.token);
    groep = antwoord;
    form.reset();
    await laadBord();
    toonBord();
  } catch (fout) {
    foutVak.textContent = fout.message;
  }
});

// ---------------------------------------------------------------- bord

async function laadBord() {
  bord = await api("GET", groepPad("/bord"));
  groep.naam = bord.groep.naam;
}

function toonBord() {
  $("#start").hidden = true;
  $("#bord-scherm").hidden = false;
  history.replaceState(null, "", `#groep-${groep.id}`);
  tekenBord();
}

function tekenBord() {
  if (!bord) return;
  document.title = `${bord.groep.naam} – Scrumboard`;
  $("#groepsnaam").textContent = bord.groep.naam;
  tekenSprint();
  tekenLeden();
  tekenKolommen();
}

function tekenSprint() {
  const s = bord.sprint;
  $("#sprint-naam").textContent = s.naam;
  $("#sprint-data").textContent = s.start || s.eind ? `${datum(s.start)} – ${datum(s.eind)}` : "";
  $("#sprint-doel").textContent = s.doel ? `Doel: ${s.doel}` : "Klik om een sprintdoel in te stellen";

  let totaal = 0, klaar = 0;
  for (const k of bord.kaarten) {
    if (!SPRINT_KOLOMMEN.includes(k.kolom)) continue;
    totaal += k.punten || 0;
    if (k.kolom === "klaar") klaar += k.punten || 0;
  }
  $("#voortgang-balk").style.width = totaal ? `${(100 * klaar) / totaal}%` : "0";
  $("#voortgang-tekst").textContent = `${klaar} / ${totaal} punten`;
}

function datum(tekst) {
  if (!tekst) return "?";
  const d = new Date(`${tekst}T12:00:00`);
  return isNaN(d) ? tekst : d.toLocaleDateString("nl-NL", { day: "numeric", month: "short" });
}

function tekenLeden() {
  const vak = $("#leden");
  vak.replaceChildren();
  for (const naam of bord.leden) {
    const chip = document.createElement("span");
    chip.className = "lid";
    const weg = document.createElement("button");
    weg.textContent = "×";
    weg.title = `${naam} verwijderen`;
    weg.addEventListener("click", async () => {
      if (!confirm(`${naam} uit het team halen?`)) return;
      await actie(() => api("DELETE", groepPad("/leden"), { naam }));
    });
    chip.append(avatar(naam), document.createTextNode(naam), weg);
    vak.append(chip);
  }

  const filter = $("#filter");
  const gekozen = filter.value;
  filter.replaceChildren(new Option("iedereen", ""), new Option("niet toegewezen", "-"));
  for (const naam of bord.leden) filter.append(new Option(naam, naam));
  filter.value = [...filter.options].some((o) => o.value === gekozen) ? gekozen : "";
}

$("#nieuw-lid").addEventListener("submit", async (e) => {
  e.preventDefault();
  const naam = e.target.naam.value;
  e.target.reset();
  await actie(() => api("POST", groepPad("/leden"), { naam }));
});

$("#filter").addEventListener("change", tekenKolommen);

function tekenKolommen() {
  const vak = $("#bord");
  const filter = $("#filter").value;
  vak.replaceChildren();
  for (const kolom of KOLOMMEN) {
    const kaarten = bord.kaarten.filter((k) => k.kolom === kolom.id);
    const sectie = document.createElement("section");
    sectie.className = "kolom";
    sectie.dataset.kolom = kolom.id;

    const kop = document.createElement("div");
    kop.className = "kolom-kop";
    const h2 = document.createElement("h2");
    h2.textContent = kolom.naam;
    const teller = document.createElement("span");
    teller.className = "teller";
    teller.textContent = kaarten.length;
    h2.append(teller);
    const plus = document.createElement("button");
    plus.textContent = "+";
    plus.title = `Kaartje toevoegen aan ${kolom.naam}`;
    plus.addEventListener("click", () => openKaart(null, kolom.id));
    kop.append(h2, plus);

    const lijst = document.createElement("div");
    lijst.className = "kaarten";
    for (const k of kaarten) {
      const zichtbaar = !filter || (filter === "-" ? !k.eigenaar : k.eigenaar === filter);
      lijst.append(maakKaart(k, zichtbaar));
    }
    sectie.append(kop, lijst);
    maakDropzone(sectie, lijst);
    vak.append(sectie);
  }
}

function maakKaart(k, zichtbaar) {
  const el = document.createElement("article");
  el.className = `kaart ${k.prioriteit}${zichtbaar ? "" : " gedimd"}`;
  el.draggable = true;
  el.tabIndex = 0;
  el.dataset.id = k.id;
  el.title = `Prioriteit: ${k.prioriteit}`;

  const titel = document.createElement("div");
  titel.className = "kaart-titel";
  titel.textContent = k.titel;
  el.append(titel);

  if (k.omschrijving) {
    const tekst = document.createElement("div");
    tekst.className = "kaart-tekst";
    tekst.textContent = k.omschrijving;
    el.append(tekst);
  }

  const voet = document.createElement("div");
  voet.className = "kaart-voet";
  const punten = document.createElement("span");
  if (k.punten !== null) {
    punten.className = "punten";
    punten.textContent = `${k.punten} pt`;
  }
  voet.append(punten);
  if (k.eigenaar) voet.append(avatar(k.eigenaar));
  el.append(voet);

  el.addEventListener("click", () => openKaart(k));
  el.addEventListener("keydown", (e) => { if (e.key === "Enter") openKaart(k); });
  el.addEventListener("dragstart", (e) => {
    gesleept = k;
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/plain", String(k.id));
    requestAnimationFrame(() => el.classList.add("sleept"));
  });
  el.addEventListener("dragend", () => {
    gesleept = null;
    el.classList.remove("sleept");
    document.querySelectorAll(".kolom.over").forEach((x) => x.classList.remove("over"));
  });
  return el;
}

function maakDropzone(sectie, lijst) {
  sectie.addEventListener("dragover", (e) => {
    if (!gesleept) return;
    e.preventDefault();
    sectie.classList.add("over");
  });
  sectie.addEventListener("dragleave", (e) => {
    if (!sectie.contains(e.relatedTarget)) sectie.classList.remove("over");
  });
  sectie.addEventListener("drop", async (e) => {
    e.preventDefault();
    sectie.classList.remove("over");
    if (!gesleept) return;
    const kaart = gesleept;
    const kolom = sectie.dataset.kolom;

    // Zoek tussen welke kaartjes is losgelaten en kies een positie daartussen.
    const anderen = [...lijst.querySelectorAll(".kaart")].filter((el) => el.dataset.id !== String(kaart.id));
    const index = anderen.findIndex((el) => {
      const r = el.getBoundingClientRect();
      return e.clientY < r.top + r.height / 2;
    });
    const posVan = (el) => bord.kaarten.find((k) => String(k.id) === el.dataset.id).positie;
    let positie;
    if (anderen.length === 0) positie = 1;
    else if (index === -1) positie = posVan(anderen[anderen.length - 1]) + 1;
    else if (index === 0) positie = posVan(anderen[0]) - 1;
    else positie = (posVan(anderen[index - 1]) + posVan(anderen[index])) / 2;

    // Meteen tonen, daarna opslaan.
    kaart.kolom = kolom;
    kaart.positie = positie;
    bord.kaarten.sort((a, b) => a.positie - b.positie || a.id - b.id);
    tekenBord();
    await actie(() => api("PUT", groepPad(`/kaarten/${kaart.id}`), { kolom, positie }));
  });
}

function avatar(naam) {
  const el = document.createElement("span");
  el.className = "avatar";
  el.title = naam;
  el.textContent = naam.split(/\s+/).filter(Boolean).slice(0, 2).map((w) => w[0].toUpperCase()).join("");
  let h = 0;
  for (const c of naam) h = (h * 31 + c.codePointAt(0)) % 360;
  el.style.background = `hsl(${h} 55% 45%)`;
  return el;
}

// ---------------------------------------------------------------- kaart-dialoog

function openKaart(k, kolom = "backlog") {
  bewerkKaart = k;
  const dialoog = $("#kaart-dialoog");
  const form = dialoog.querySelector("form");
  form.querySelector(".fout").textContent = "";
  $("#kaart-kop").textContent = k ? "Kaartje bewerken" : "Nieuw kaartje";
  $("#kaart-verwijder").hidden = !k;

  form.eigenaar.replaceChildren(new Option("niemand", ""));
  const namen = [...bord.leden];
  if (k?.eigenaar && !namen.includes(k.eigenaar)) namen.push(k.eigenaar);
  for (const naam of namen) form.eigenaar.append(new Option(naam, naam));
  form.kolom.replaceChildren(...KOLOMMEN.map((c) => new Option(c.naam, c.id)));

  form.titel.value = k?.titel ?? "";
  form.omschrijving.value = k?.omschrijving ?? "";
  form.eigenaar.value = k?.eigenaar ?? "";
  form.punten.value = k?.punten ?? "";
  if (k?.punten != null && form.punten.value === "") {
    form.punten.append(new Option(k.punten));
    form.punten.value = k.punten;
  }
  form.prioriteit.value = k?.prioriteit ?? "normaal";
  form.kolom.value = k?.kolom ?? kolom;
  dialoog.showModal();
  form.titel.focus();
}

$("#kaart-dialoog form").addEventListener("submit", async (e) => {
  const form = e.target;
  const knop = e.submitter?.value;
  if (knop === "annuleer") return;
  e.preventDefault();
  try {
    if (knop === "verwijder") {
      if (!confirm("Dit kaartje verwijderen?")) return;
      await api("DELETE", groepPad(`/kaarten/${bewerkKaart.id}`));
    } else {
      const gegevens = {
        titel: form.titel.value,
        omschrijving: form.omschrijving.value,
        eigenaar: form.eigenaar.value,
        punten: form.punten.value === "" ? null : Number(form.punten.value),
        prioriteit: form.prioriteit.value,
      };
      if (!bewerkKaart) {
        gegevens.kolom = form.kolom.value;
        await api("POST", groepPad("/kaarten"), gegevens);
      } else {
        if (form.kolom.value !== bewerkKaart.kolom) {
          gegevens.kolom = form.kolom.value;
          const laatste = bord.kaarten.filter((k) => k.kolom === gegevens.kolom).at(-1);
          gegevens.positie = laatste ? laatste.positie + 1 : 1;
        }
        await api("PUT", groepPad(`/kaarten/${bewerkKaart.id}`), gegevens);
      }
    }
    $("#kaart-dialoog").close();
    await actie(() => null);
  } catch (fout) {
    form.querySelector(".fout").textContent = fout.message;
  }
});

// ---------------------------------------------------------------- sprint-dialoog

function openSprint() {
  const dialoog = $("#sprint-dialoog");
  const form = dialoog.querySelector("form");
  form.querySelector(".fout").textContent = "";
  for (const veld of ["naam", "doel", "start", "eind"]) form[veld].value = bord.sprint[veld];
  dialoog.showModal();
}

$("#sprint").addEventListener("click", openSprint);
$("#sprint").addEventListener("keydown", (e) => { if (e.key === "Enter") openSprint(); });

$("#sprint-dialoog form").addEventListener("submit", async (e) => {
  const form = e.target;
  const knop = e.submitter?.value;
  if (knop === "annuleer") return;
  e.preventDefault();
  try {
    if (knop === "opruimen") {
      const klaar = bord.kaarten.filter((k) => k.kolom === "klaar");
      if (!klaar.length) throw new Error("De kolom ‘Klaar’ is al leeg.");
      if (!confirm(`${klaar.length} afgeronde kaartje(s) definitief verwijderen?`)) return;
      for (const k of klaar) await api("DELETE", groepPad(`/kaarten/${k.id}`));
    } else {
      await api("PUT", groepPad("/sprint"), {
        naam: form.naam.value,
        doel: form.doel.value,
        start: form.start.value,
        eind: form.eind.value,
      });
    }
    $("#sprint-dialoog").close();
    await actie(() => null);
  } catch (fout) {
    form.querySelector(".fout").textContent = fout.message;
  }
});

// ---------------------------------------------------------------- algemeen

async function actie(stap) {
  try {
    await stap();
  } catch (fout) {
    alert(fout.message);
  }
  await ververs();
}

async function ververs() {
  if (!groep) return;
  try {
    await laadBord();
    tekenBord();
  } catch (fout) {
    if (fout.status === 403) {
      vergeetToken(groep.id);
      alert("De toegang tot deze groep is verlopen. Log opnieuw in.");
      toonStart();
    }
  }
}

$("#terug").addEventListener("click", toonStart);

// Houd het bord bij als groepsgenoten iets aanpassen.
setInterval(() => {
  const bezig = gesleept || document.querySelector("dialog[open]") || document.hidden;
  if (groep && !bezig) ververs();
}, VERVERS_MS);

// Opstarten: direct naar de groep als die in de link staat.
(async () => {
  const id = Number((location.hash.match(/^#groep-(\d+)$/) || [])[1]);
  await toonStart();
  const g = id ? (await api("GET", "/api/groepen")).find((x) => x.id === id) : null;
  if (g) openGroep(g);
})();
