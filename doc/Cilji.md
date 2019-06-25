# Trubadur cilji

 

### Vpisovanje ritmičnih vaj v aplikacijo

- To dela že kar dobro, je še precej bugov, ampak kakorkoli, važno je da nekako dela.

    **PREDNOSTNO**

- Treba je dodati še podporo za različne taktovske načine

- Preveri, ali se da izpolnjevati Šavlijeve vaje in jih vpiši z vmesnikom

- Triole 💩💩😢

 

### Preverjanje ritmičnih vaj in dajanje feedbacka uporabniku

 

- Imamo Diff pogled, 

- Imamo gumb za pregled:

    - Naj ima gumb za pregled omejeno število pritiskov: Lahko ga pritisne največ trikrat, potem gre vaja naprej. Kolikokrat lahko pritisne gumb, naj bo parameter igre, ki naj ga pošlje server.
    - Če je igra vaja, potem naj bo velikokrat

    

 ### Generiranje ritmičnih vaj

- To še ne dela niti približno.
- Naj generiranje deluje tako:
    - Naj bo za začetek vpisanih 100 vaj. Vsak uporabnik naj naključno rešuje
    - Naj bo 30 vaj “za vajo” in 80 vaj za zaresno igro.
- Kako naj bo organizirana igra:
    - Uporabnik naj dobi 8 različnih vaj na igro.
    - Sistem naj naključno razporedi naslednje intervale
        - Nekaj naj jih bo takih, ki jih je že rešil v preteklosti
        - Nekaj naj bo novih
    - Ko **en uporabnik** reši vse ritmične vaje, naj sistem opozori administratorje, da so že vse vaje rešene in da rabimo nove.

 

### Analiza vaj za učitelje

- Katere podatke sistem zbira
    - Ali je uspešno rešil vajo (rešil / preskočil / ni uspel rešiti v omejenih poskusih)
    - Koliko napačnih poskusov oddaje na vajo
    - Kolikokrat je pritisnil **delete**
    - Kolikokrat je pritisnil **ponovno predvajanje**
    - Koliko časa je rabil da je rešil vajo
    - O vajah
        - Katere vaje je rešil
        - Vnesena rešitev, če vaje ni uspešno rešil oziroma jo je preskočil
- Sistem iz teh faktorje izračuna težavnost vaje za uporabnika
- Naj izračunava tudi povprečno težavnost
    - Če je vaja težka, jo večkrat predlaga uporabniku

- Rabimo spletno stran na katero se je treba prijaviti (uporabi obstoječ API, ki že dela za učence)
- Učitelj lahko vidi igre učencev. Lahko vidi neke statistike



### Intuitiven vmesnik 

- Triole so obupno neintuitivne

- Kako bi blo bolje:

    - Pritisneš gumb za triole
    - Triola se obarva z drugo barvo, tako da je tudi pri urejanju jasno, da se ureja to triolo
    - Prvo noto ali pavzo - ta določi vrednost triole: če je četrtinka, bodo samo četrtinke v tej trioli
    - Potem naklofa tolk not kolikor hoče in se potem triola vleče naprej - če so 4 note, je kvartola itd itd..
    - Ko želi končati to triolo, pritisne gumb za triole ali katerokoli drugo vrednost, ki ni v trioli

- Vprašanja: 

    - kako popravljati triolo?

        - Kako izbrisati eno noto? 

            Pritisneš delete in se izbriše samo ena nota, vrednost triole se ustrezno prilagodi

        - Kako izbrisati triolo?

            Izbrišeš zadnjo noto v trioli

        - Kaj če izbrišeš prvo noto v trioli?

            - Če je triola dolga 1, potem se triola izbriše
            - Če je triola daljša od 1, potem se začetek triole premakne na naslednjo noto, ta nota se pa izbriše

        - Kaj se zgodi, če si v sredini triole in pritisneš neko noto?

            - Če je iste vrednosti, kot triola, potem se doda v sredino, vrednost triole se popravi
            - Če je druge vrednosti, kot je triola, ali je takt ali pa pika, potem ne naredi nič, javi napako

        - Kako dodaš nove note v triolo:

            - Ko si za triolo, se nad kursorjem pokaže gumb uredi triolo. 
            - Če ga pritisneš, se triola označi in lahko dodajaš nove vrednosti

        - Kako to vpliva na interno predstavitev not?

            - Lahko ostane enaka, 
            - Pri dodajanju bo treba spreminjati tip triole, ampak to ne bo trajalo tako dolgo



Torej kako to spremenit?

- ✅ Spremeni gumb na tipkovnici 
- Spremeni delovanje NoteStore
    - ✅ V cursor dodaj editing_tuplet
    - Ko pritisneš na triola gumb nastavi editing_tuplet
    - Pol dej v noteStore ClickListener še metodo alter_tuplet, ki popravi trenutno triolo

Implement:

- Tuplet button + correct note
- Tuplet button + correct notes + tuplet button



### Prikupen, lep vmesnik

- Pa ja, kr v redu je…



### Odziven vmesnik 

Na tem je še treba delati…

-  Za telefone je treba naredit, da je OnTouchStart
- V NoteStore je treba odstranit this.callRender() in naredit, da se rendra samo ko je res treba 
- Treba je zmanjšat količino JavaScripta, da se ne bo 6 MB skoz loadal
- Treba je zmanjšat MIDIjs datoteke 



### Imeti mora psihološko komponento, da bodo uporabniki aplikacijo radi uporabljali

- Treba je dodat kresničke in badge, da bo uporabnik vesel, ko bo igral igro.
- Treba je naredit zelo tekmovalno. V igri je treba skoz opominjat, kateri uporabnik je boljši od trenutnega in za koliko…



## IMPLEMENTACIJA CILJEV:

- ~~Daj ta dokument v GIT in ga sproti dopolnjuj~~

- Pojdi čez Šavlijeve vaje in jih poskušaj vpisat v aplikacijo (2h)

- Potem izvozi JSON iz uspešno vpisanih in ga daj v Exercise Generator (2h)
    - Implementiraj gumb za izpis JSONa v konzolo. 
- Kar ne dela, implementiraj sproti (??? Veliko)

- Implementiraj izbirnik naslednje vaje, zato da boš lahko testiral vnos (1.5h)

- Potem malo spedenaj uporabniški vmesnik, (poglej cilje »odziven vmesnik«)  (2h)

- Pojdi na sestanek s Petrom Šavlijem