# Sicherheit & Datensicherung

## Automatische Sicherung der ganzen Website

Die Website wird jetzt automatisch gesichert: einmal pro Monat wird die komplette Website — alle Dateien
inklusive Bilder, sowie die gesamte Datenbank — in eine einzige, mit Datum versehene Sicherungsdatei gepackt.
Ältere Sicherungen werden automatisch aufgeräumt, es bleiben immer die letzten 6 Sicherungen erhalten — also
ungefähr ein halbes Jahr Verlauf. Falls jemals etwas schiefgeht, kann die Website aus einer dieser Sicherungen
wiederhergestellt werden.

## Migrations-Schnittstellen nur bei Bedarf aktiv

Beim ursprünglichen Aufbau der Website wurden technische Schnittstellen gebaut, um grössere Mengen an Werken auf
einmal einzuspielen. Diese sind mit einem Passwort geschützt, waren aber bisher immer im Hintergrund erreichbar,
auch wenn sie nicht gebraucht wurden. Sie sind jetzt nur noch aktiv, wenn tatsächlich eine solche Übernahme
ansteht, und ansonsten komplett unsichtbar von aussen.
