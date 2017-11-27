<?php

//Lege cookie an, für übergebene Daten, die mit dem Masterschlüssel verschlüsselt sind

/*

Beim Login
- Hole Nutzer aus den anderen DBS
- Lege session an
- Setzte cookies

Nun:
- Füge noch nen Hook hinzu
- Checke ob es auf der gleichen Domain liegt
- Wenn nicht, mache das alles trotzdem
- Binde ein Bild ein, welches eine Datei aufruft auf den externen System und dann die cookies anlegt

*/