# TRUBADUR TODO



- ~~`MUST`, `SIMPLE`, `BEAUTY` V Tutorial napiši, koliko taktov bo~~
- ~~[MUST, SIMPLE, BEAUTY] V Tutorial napiš, kakšen tempo bo~~
- ~~[MUST, SIMPLE, BEAUTY] V Tutorial napiš, kakšen takt bo~~
- ~~[MUST, AVERGAE, BEAUTY] Kursor naj bo višje čez takt~~
- ~~[MUST, AVG, RESPONSIVE] Ko je telefon v landscape se prikazujejo drugačni gumbi - poštimaj da bodo enaki oz. logični za manjši zaslon, kot so v portrait modu~~
- ~~FIX smaller width weird scroll in landscape: padding-bottom v App.vue~~
- ~~[MUST, AVG, RESPONSIVE] Pri telefonih šestnajstinke štrlijo čez taktnico in niso lepo razporejene~~
    - ~~Naredi, da ne bo samo mean note width, ampak se bo sproti računalo~~
    - ~~Za 32-tinke naj bo najmanj prostora: 20~~
    - ~~Za 16 in več naj bo pa več prostora. Tako se bo vse lepo prilagajalo...~~
- ~~Set different cursor offsets for different notes~~

- ~~FIX CLICK on iPHONE - landscape experience~~

- ~~FIX Small screen scroll jumping: neki je s isHeaserSticky v App.vue~~
- ~~Centriraj bubble scroll, da ne bo skakal, ko ga pritisneš WONTFIX~~

- ~~iOS fix clicking anywhere opens URI bar and bottom bar - landscape experience~~

- ~~`MUST` `HARD` `BEAUTY` Daj drug zvok z več sustaina~~



- iOS touch fix



- FIX iOS fullpage webapp experience
    - Add icon
    - Fix orientation change problems
    - Add prompt for user to "install" the app



- [MUST, HARD, FUNCTIONAL] Triole naj delujejo drugače

    - ~~Ko si za veljavno noto, naj se prikaže gumb triola~~

    - ~~Polovinka naj se deli na 3 četrtinke (taka triola kot je zdaj)~~
    - ~~Četrtinka se deli na 3 osminke~~
    - ~~Osminka naj se deli na 3 šestnajstinke~~
    - ~~Šestnajstinka naj se deli na 3 dvaintridesetinke~~
    - ~~Ko narediš triolo, de kursor postavi pred triolo~~

    ```sequence
    User->GUI: četrtinka()
    User->GUI: triola()
    GUI->GUI: naredi tri note\nin jih obarvaj\nz modro
    GUI->GUI: Postavi se na\n začetek pred\ntriolo
    GUI->GUI: Pokaži gumbe za dobe,\nki pašejo v triolo
    User->GUI: osminka()
    GUI->GUI: Zamenja naslednjo noto z osminko
    GUI->GUI: Premakne kurzor naprej
    User->GUI: šestnajstinka()
    GUI->GUI: Naredi šestnajstinko in\n šestnajstinsko pavzo in\n pavzo pobarva z modro
    User->GUI: izbriši()
    GUI->GUI: Zamenja noto s pavzo
    GUI->GUI: Gre čez triolo in pogleda, če se da \nzdružit kake pavze. Vedno združuje\n po dve in dve
    User->GUI: triola()
    GUI->GUI: Izbriše triolo
    
    ```

    - ~~Triola - naj ima trajanje od prejšnje note~~
    - Če se user kam premakne, tako da v bližini ni overwrite not, potem odstrani overwrite iz vseh not
    - ~~Če ima nota overwrite, ji daj moder stajl v StaveView in odstrani stajling v NoteStore~~
    - ~~Ne uporabi tuplet_from in tuplet_to ampak samo tuplet_end=true in potem sproti preštevaj...~~
- ~~Poštimaj duration v triolah~~
  
- ~~Ko si v trioli se more dat dodajat druge note v triolo,~~
    - IMPLEMENTIRAJ dodajanje različnih dob v triolo
    - FIX tuplet stems - če daš osminko pred triolo se napačno vežejo 
    - Naredit moreš DUOLO, TRIOLO, PENTOLO, SEKSTOLO...



- ~~IZKLOPIL SEM BEAMSE~~

- ~~[MUST, FUNCTIONAL] Drugače razporedi tipkovnico~~

    - ~~Naj bodo tudi 32-tinke~~
    - ~~Naj se hkrati vidijo vse note in vse pavze~~
    - ~~Naredi še eno vrstico~~

    

- ~~[SHOULD, FUNCTIONAL] NAREDI SLIDING BUTTON! :D Tko da pritisneš in podrsaš al levo al desno in se spremeni neka vrednost~~

      

- ~~[SHOULD, HARD, LOTS OF WORK, FUNCTIONAL] Ko uporabnik stisne preglej, naj se prikaže DIFF med njegovim vpisanim in pravilnim odgovorom~~

    - Naj se obe verziji tudi predvajata, naj bodo spodaj drugačni gumbi MAYBE
    - Ko se predvaja en del, naj se osvetlijo vsi takti MAYBE



- Make infinite scroll?

- ~~`MUST` `HARD` `FUNCTIONAL` Gumb za nov takt~~



- `MUST` `AVG` `FUNCTIONAL` Kombinirani takti 
    - Pri odštevanju odštej oba takta



- ~~`MUST` `EASY` `BEAUTY` Skrij gumb za  predvajanje uporabniku~~

    

- [MUST, HARD, RESPONSIVE] Testiraj višino na višjih telefonih in pri višjih telefonih (poglej na Google, kateri screen sizi so pogosti danes) napiši Media Querije, da se bo lepše izrisalo

- ~~`MUST` `EASY` `BEAUTY` SexyButton ozadje ne sme biti zamaknjeno - naredi za vse take gumbe~~

- `MUST` `HARD` `RESPONSIVE` Stestiraj rotacijo na več napravah



- [MUST, HARD] FIX CURSOR RENDERING (daj v svojo metodo in naj se ne izrisuje samo v _render)
- [MUST, HARD] Zmanjšaj število stvari v _render_context funkciji
- [MUST, HARD] Odstrani _call_render v noteStore



- `SHOULD` `ASAP` Implementiraj statistiko - poglej, kako je Žiga to delal



- `SHOULD` `HARD` Gumbi naj bodo na TouchStart, da bo bolj responsive
- `SHOULD` `HARD` Poglej, kako bi lahko izboljšal izkušnjo na Androidu