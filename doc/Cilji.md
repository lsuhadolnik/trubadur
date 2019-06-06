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

- Daj ta dokument v GIT in ga sproti dopolnjuj

- Pojdi čez Šavlijeve vaje in jih poskušaj vpisat v aplikacijo (2h)

- Potem izvozi JSON iz uspešno vpisanih in ga daj v Exercise Generator (2h)
    - Implementiraj gumb za izpis JSONa v konzolo. 
- Kar ne dela, implementiraj sproti (??? Veliko)

- Implementiraj izbirnik naslednje vaje, zato da boš lahko testiral vnos (1.5h)

- Potem malo spedenaj uporabniški vmesnik, (poglej cilje »odziven vmesnik«)  (2h)

- Pojdi na sestanek s Petrom Šavlijem