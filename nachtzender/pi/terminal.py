"""Een mini-terminal voor de Linux-post in de browser.

Kent alleen ls, cd, pwd, cat, grep, head, whoami, echo en help. Alles gebeurt
binnen de map van de Linux-post: '~' is die map, en hoger komt niemand.
"""

import fnmatch
import shlex
from pathlib import Path

MAX_REGELS = 200

HULP = """\
Beschikbare commando's:
  ls [-a] [map]          laat bestanden zien (-a: ook verborgen bestanden)
  cd map                 ga een map in   (cd .. = terug, cd ~ = begin)
  pwd                    in welke map ben ik?
  cat bestand            toon een bestand
  head bestand           toon de eerste 10 regels
  grep WOORD bestand(en) zoek WOORD, bv. grep STATUS *.log
  grep -r WOORD map      zoek ook in alle submappen, bv. grep -r WOORD .
  clear                  scherm leegmaken"""


class Fout(Exception):
    pass


def _virtueel(root, pad):
    rel = pad.relative_to(root).as_posix()
    return "~" if rel == "." else "~/" + rel


def _los_op(root, cwd, invoer):
    """Zet een pad uit de terminal om naar een echt pad binnen root."""
    if invoer in ("~", ""):
        return root
    basis = root if invoer.startswith("~") else cwd
    invoer = invoer.lstrip("~").lstrip("/") if invoer.startswith("~") else invoer
    if invoer.startswith("/"):
        raise Fout("Toegang geweigerd: je mag alleen in je eigen map (~) kijken.")
    pad = (basis / invoer).resolve()
    if pad != root and root not in pad.parents:
        raise Fout("Toegang geweigerd: je mag alleen in je eigen map (~) kijken.")
    return pad


def _glob(root, cwd, argument):
    if not any(t in argument for t in "*?["):
        return [_los_op(root, cwd, argument)]
    map_ = _los_op(root, cwd, str(Path(argument).parent) if "/" in argument else ".")
    patroon = Path(argument).name
    treffers = sorted(p for p in map_.iterdir() if fnmatch.fnmatch(p.name, patroon) and not p.name.startswith("."))
    if not treffers:
        raise Fout(f"{argument}: geen bestanden gevonden")
    return treffers


def _toon_pad(cwd, pad):
    try:
        return pad.relative_to(cwd).as_posix()
    except ValueError:
        return pad.as_posix()


def _ls(root, cwd, args):
    alles = "-a" in args or "-la" in args or "-al" in args
    mappen = [a for a in args if not a.startswith("-")] or ["."]
    uit = []
    for m in mappen:
        pad = _los_op(root, cwd, m)
        if not pad.exists():
            raise Fout(f"ls: {m}: bestand of map bestaat niet")
        if pad.is_file():
            uit.append(pad.name)
            continue
        namen = sorted(p.name + ("/" if p.is_dir() else "") for p in pad.iterdir()
                       if alles or not p.name.startswith("."))
        uit.append("  ".join(namen) if namen else "")
    return "\n".join(uit)


def _lees(root, cwd, naam, commando):
    pad = _los_op(root, cwd, naam)
    if pad.is_dir():
        raise Fout(f"{commando}: {naam}: is een map")
    if not pad.exists():
        raise Fout(f"{commando}: {naam}: bestand bestaat niet")
    return pad.read_text(encoding="utf-8", errors="replace").splitlines()


def _grep(root, cwd, args):
    opties = {a for a in args if a.startswith("-")}
    rest = [a for a in args if not a.startswith("-")]
    if not rest:
        raise Fout("grep: wat moet ik zoeken? Bijvoorbeeld: grep STATUS *.log")
    woord, doelen = rest[0], rest[1:]
    recursief = bool(opties & {"-r", "-R", "-ri", "-ir"})
    hoofdletters = bool(opties & {"-i", "-ri", "-ir"})
    if not doelen:
        if not recursief:
            raise Fout("grep: in welk bestand? Bijvoorbeeld: grep STATUS *.log  of  grep -r STATUS .")
        doelen = ["."]
    bestanden = []
    for doel in doelen:
        for pad in _glob(root, cwd, doel):
            if pad.is_dir():
                if not recursief:
                    raise Fout(f"grep: {doel}: is een map (gebruik grep -r)")
                bestanden += sorted(p for p in pad.rglob("*") if p.is_file())
            elif pad.exists():
                bestanden.append(pad)
            else:
                raise Fout(f"grep: {doel}: bestand bestaat niet")
    zoek = woord.lower() if hoofdletters else woord
    meerdere = len(bestanden) > 1 or recursief
    uit = []
    for pad in bestanden:
        for regel in pad.read_text(encoding="utf-8", errors="replace").splitlines():
            if zoek in (regel.lower() if hoofdletters else regel):
                uit.append(f"{_toon_pad(cwd, pad)}:{regel}" if meerdere else regel)
    return "\n".join(uit)


def voer_uit(root, cwd_virtueel, regel):
    """Voer één commandoregel uit. Geeft (uitvoer, nieuwe cwd)."""
    root = Path(root).resolve()
    if not root.exists():
        return "De zender heeft nog geen bestanden: er loopt geen missie.", "~"
    try:
        cwd = _los_op(root, root, cwd_virtueel or "~")
        if not cwd.is_dir():
            cwd = root
    except Fout:
        cwd = root
    try:
        delen = shlex.split(regel)
    except ValueError:
        return "Er klopt iets niet met de aanhalingstekens.", _virtueel(root, cwd)
    if not delen:
        return "", _virtueel(root, cwd)
    if any(t in regel for t in ("|", ">", ";", "&&")):
        return "Deze terminal kent geen |, >, ; of &&. Eén commando tegelijk!", _virtueel(root, cwd)

    commando, args = delen[0], delen[1:]
    try:
        if commando == "help":
            uit = HULP
        elif commando == "ls":
            uit = _ls(root, cwd, args)
        elif commando == "cd":
            doel = _los_op(root, cwd, args[0] if args else "~")
            if not doel.is_dir():
                raise Fout(f"cd: {args[0]}: map bestaat niet")
            cwd, uit = doel, ""
        elif commando == "pwd":
            uit = _virtueel(root, cwd).replace("~", "/home/agent", 1)
        elif commando == "cat":
            if not args:
                raise Fout("cat: welk bestand? Bijvoorbeeld: cat LEESMIJ.txt")
            uit = "\n".join(r for a in args for p in _glob(root, cwd, a)
                            for r in _lees(root, cwd, _toon_pad(cwd, p), "cat"))
        elif commando == "head":
            if not args:
                raise Fout("head: welk bestand?")
            uit = "\n".join(_lees(root, cwd, args[-1], "head")[:10])
        elif commando == "grep":
            uit = _grep(root, cwd, args)
        elif commando == "whoami":
            uit = "agent"
        elif commando == "echo":
            uit = " ".join(args)
        elif commando in ("sudo", "rm", "su"):
            uit = f"{commando}: netjes geprobeerd, agent. Dat mag hier niet. 😉"
        else:
            uit = f"{commando}: commando niet gevonden. Typ 'help' voor een lijstje."
    except Fout as fout:
        uit = str(fout)

    regels = uit.splitlines()
    if len(regels) > MAX_REGELS:
        uit = "\n".join(regels[:MAX_REGELS]) + f"\n... ({len(regels) - MAX_REGELS} regels meer; zoek gerichter met grep)"
    return uit, _virtueel(root, cwd)
