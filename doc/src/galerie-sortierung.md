# Die Galerie: Werke ordnen

Du kannst die Reihenfolge der Werke innerhalb einer Kollektion (z. B. "Japanreise") jetzt selbst per
Drag-and-Drop festlegen — im Werke-Bereich im Backend, gefiltert auf eine bestimmte Kollektion, lassen sich die
Zeilen am Griff-Symbol greifen und neu anordnen. Diese Reihenfolge wird dann sowohl in der Verwaltung als auch auf
der öffentlichen Kollektionsseite verwendet — und bestimmt auch, in welcher Reihenfolge man beim Blättern
zwischen einzelnen Werken landet (siehe nächstes Kapitel).

Technisch war das komplizierter als gedacht: Ein Werk kann in mehreren Kollektionen gleichzeitig vorkommen, und
die Reihenfolge musste pro Kollektion unabhängig funktionieren — ein Werk kann also in "Japanreise" ganz vorne
stehen und in einer anderen Kollektion ganz hinten, ohne dass sich das gegenseitig beeinflusst. Der erste
Lösungsansatz hätte das nicht geschafft und wurde noch am selben Tag durch die jetzige, robustere Lösung ersetzt.

Neue Werke, die frisch einer Kollektion zugeordnet werden, erscheinen automatisch am Ende der Liste — nichts muss
manuell nachgezogen werden. Der Drag-Griff ist zudem bewusst deaktiviert, solange die Liste nicht auf eine
einzelne Kollektion gefiltert ist, damit nichts aus Versehen durcheinandergerät.

## Bilder werden automatisch in der richtigen Grösse geladen

Die Bild-Kacheln in der Galerie, auf der Startseite und in der Veranstaltungsübersicht laden jetzt automatisch
die passende Bildgrösse für den jeweiligen Bildschirm, statt immer die grösstmögliche Version zu laden. Das macht
die Seite spürbar schneller. Bereits veröffentlichte Werke mussten dafür einmal neu gespeichert werden, damit sie
die neue Technik übernehmen; neue oder frisch bearbeitete Werke funktionieren automatisch.
