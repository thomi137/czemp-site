# Einzelne Werke: Blättern zwischen Bildern

Auf der Seite eines einzelnen Werks kann man zum nächsten oder vorherigen Werk in derselben Kollektion wechseln —
mit den Pfeiltasten der Tastatur, per Wischen auf dem Handy/Tablet, oder über die Pfeil-Buttons am linken/rechten
Bildschirmrand (nur mit Maus sichtbar). Die Reihenfolge folgt genau der Reihenfolge, die im Backend per
Drag-and-Drop festgelegt wurde (siehe voriges Kapitel), und springt vom letzten Werk wieder zum ersten und
umgekehrt.

Diese Funktion ist über drei Ausbaustufen gewachsen:

1. **Erste Version** — einfache Vor/Zurück-Links, die die Seite jeweils komplett neu laden.
2. **Sanftere Übergänge** — beim Wechseln blendet das alte Bild sanft aus und das neue ein, bzw. rutscht je nach
   Richtung von links oder rechts ins Bild, statt einfach hart umzuschalten.
3. **Echtes Blättern ohne Neuladen** (aktuellster Stand) — die Seite lädt beim Wechseln nicht mehr komplett neu,
   nur noch Bild, Titel, Beschreibung und Preis werden ausgetauscht; Menü und Fusszeile bleiben stehen. Das fühlt
   sich spürbar schneller und "runder" an, wie eine echte Bildergalerie. Funktioniert weiterhin ganz normal, falls
   im Browser einmal JavaScript deaktiviert ist oder die Verbindung mitten im Wechsel abbricht — dann springt die
   Seite einfach auf die klassische Variante mit komplettem Neuladen zurück.

Die runden Pfeil-Buttons wurden ausserdem optisch überarbeitet: statt eckiger, durchgehend schwarzer Buttons gibt
es jetzt dezente Kreise mit Rand, die beim Darüberfahren mit der Maus zu satten Schwarz mit weissem Pfeil
wechseln.

> Seit Anfang September gehört zum Blättern auch ein zweites Bild pro Werk zuverlässig dazu — siehe
> [Sekundärbild: zweites Bild pro Werk](sekundaerbild.md). Und das Hauptbild lässt sich jetzt anklicken, um es
> bildschirmfüllend zu sehen — siehe [Vollbildansicht](vollbildansicht.md).

## Bild randabschliessend

Das Hauptbild eines Werks lag ursprünglich nicht bündig mit Logo und Titel darüber — das wurde behoben. Wie das
Bild seither generell dargestellt wird (Grösse, Lücken zur Textspalte) hat sich seit Anfang September nochmals
geändert — siehe [Sekundärbild: zweites Bild pro Werk](sekundaerbild.md).
