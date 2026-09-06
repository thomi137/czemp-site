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

## Sekundärbild: zweites Bild jetzt über ein eigenes Feld

Ein zweites/drittes Foto zu einem Werk (z. B. bei "The Lovestory from Asakusa" oder "Blitzgedanken", siehe
[Deine Rückmeldungen (Ende August)](rueckmeldungen-august.md)) lag bisher direkt im Beschreibungstext. Das hatte
einen Nachteil beim neuen "Blättern ohne Neuladen" oben: da dieses Zusatzbild Teil des freien Textes war, wusste
die Wechsel-Funktion nicht, dass sie es beim Blättern mit austauschen muss — im schlimmsten Fall blieb beim
Wechsel zum nächsten Werk das Zusatzbild des vorherigen Werks stehen.

Es gibt daher jetzt ein eigenes **"Sekundärbild"-Feld** in der Seitenleiste beim Bearbeiten eines Werks, genau wie
beim Preis-Feld: Foto auswählen, fertig. Damit weiss die Seite immer genau, ob und welches Zusatzbild zu einem
Werk gehört, und tauscht es beim Blättern zuverlässig mit aus.

Alle 32 Werke, die bereits ein solches Zusatzbild im Text hatten, wurden automatisch auf das neue Feld
umgestellt — für Besucher der Website sieht dabei nichts anders aus, nur die Technik dahinter ist robuster.

## Bilder zeigen jetzt immer die volle Breite

Haupt- und Sekundärbild eines Werks werden jetzt so gross wie möglich dargestellt: sie füllen immer die ganze
verfügbare Breite aus, ohne Beschnitt. Vorher wurden hochformatige Fotos in der Höhe begrenzt, damit die Seite
nicht zu lang wird — dafür blieb bei schmalen Fotos oft ungenutzter Platz übrig. Jetzt ist es umgekehrt: ein sehr
hohes Foto kann die Seite etwas länger machen als vorher, dafür wird nie mehr etwas vom Bild abgeschnitten oder
unnötig verkleinert.
