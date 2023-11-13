# Objecten Tabelen
Het publiceren van dynamische tabellen in WordPress aan de hand van de Overige Objecten Standaard van de VNG (Vereniging van Nederlandse Gemeenten) stelt gemeenten in staat om eenvoudig en efficiënt gegevens uit hun datawarehouse te publiceren op hun websites. 
Door gebruik te maken van deze standaard, kunnen gemeenten gestructureerde data naadloos integreren in hun WordPress-sites, waarbij de gegevens dynamisch worden bijgewerkt om actuele en accurate informatie weer te geven. 
Dit proces automatiseert de dataflow, vermindert de kans op fouten, en zorgt voor consistentie in de gegevenspresentatie, waardoor burgers toegang krijgen tot betrouwbare en up-to-date informatie.

Deze aanpak biedt aanzienlijke voordelen voor zowel gemeenten als burgers. 
Voor gemeenten betekent het een vereenvoudiging van het beheer van online content, waarbij tijdrovende handmatige updates worden geëlimineerd. 
Burgers profiteren van een verbeterde toegang tot informatie, waardoor de transparantie en de dienstverlening van de gemeente worden verhoogd. 
Door het gebruik van dynamische tabellen kunnen gegevens zoals gemeentelijke voorzieningen, projectupdates, statistieken, en meer, direct en op een gebruiksvriendelijke manier worden gepresenteerd, wat bijdraagt aan een beter geïnformeerde en betrokken gemeenschap.

## Hoe te gebruiken
1. Upload de plugin .zip op de Plugins-pagina.
2. Activeer de plugin.
3. Ga naar Instellingen -> ObjectTabel-pagina.
4. Voeg een configuratie toe met API URL en API KEY.
5. U kunt ervoor kiezen om een CSS-klasse toe te voegen aan de tabel als er een is.
6. U kunt kiezen om een optionele mapping JSON toe te voegen, bijvoorbeeld {"name": "naam"}.
7. Voeg een shortcode toe aan een gewenste pagina zoals: [object-tabel configId="2"], waarbij configId de id is van een van uw configuraties op de instellingenpagina.
8. Bij het bezoeken van de pagina zal de shortcode code activeren die de gegevens ophaalt en een tabel weergeeft.

## Afhankelijkheden
Deze plugin vereist een Overige Objecten Registratie als enige afhankelijkheid om effectief te kunnen functioneren. 
Een Overige Objecten Registratie stelt de plugin in staat om gestructureerde data te verkrijgen en te presenteren in dynamische tabellen op uw WordPress-site. 
Voor het optimaal benutten van deze afhankelijkheid, adviseren wij het gebruik van een Common Gateway.

Een Common Gateway faciliteert de naadloze integratie van data uit verschillende bronnen, inclusief Overige Objecten Registraties, door een uniforme toegangspoort te bieden. 
Het stelt de plugin in staat om data dynamisch te verkrijgen en bij te werken, wat essentieel is voor het accuraat en actueel weergeven van informatie in de tabellen. 
De gateway zorgt voor een gestroomlijnde en beveiligde dataflow, wat bijdraagt aan de betrouwbaarheid en efficiëntie van de plugin.

### De Common Gateway configureren ###
Om gebruik te maken van de Common Gateway als Overige Objecten Registratie voor de objecten tabel plugin moet je een aantal dingen in de Common Gateway op de juiste manier configureren.
Na [installeren van de Common Gateway](https://commongateway.readthedocs.io/en/latest/Installation/) is ook de Gateway UI beschikbaar. Via de Gateway UI kunnen we een aantal dingen configureren.

Als eerste willen we een Schema configureren via -> `Schemas` -> `Add Schema` (Klik [hier](https://commongateway.github.io/CoreBundle/pages/Features/Schemas) voor meer informatie over Schemas).\
Een Schema bevat de beschrijving van hoe een Object er uit moet/kan zien. 
Vul een naam, beschrijving en een unieke reference (url zoals: https://jouwDomein.nl/naamVanJouwSchema.schema.json) in en maak een Schema aan.

Dan willen aan dit nieuwe Schema properties toevoegen via het tab -> `Properties` -> `Add Property`. 
De properties zijn de verschillende velden wat een Object kan (of moet) hebben. 
Deze velden kunnen later als kolommen getoond worden in een objecten tabel.

> **Opmerking:**
> Als je later via de Gateway UI een excel bestand van objecten wilt kunnen importeren is het verstandig om hier alleen velden van type = String toe te voegen aan je Schema.

Om te regelen dat de objecten tabel plugin via een API URL objecten uit de gateway kan ophalen moeten we een Endpoint toevoegen aan de Gateway via -> `Endpoints` -> `Add Endpoint` (Klik [hier](https://commongateway.github.io/CoreBundle/pages/Features/Endpoints) voor meer informatie over Endpoints).\
Maak een Endpoint aan voor elke Schema die je hebt toegevoegd. Geef elk Endpoint een naam, unieke reference (url zoals: https://jouwDomein.nl/naamVanJouwEndpoint.endpoint.json), selecteer de Schema waarvoor je het Endpoint maakt en vink bij Methods "GET" aan.\
Path en Path Regex bepalen hoe je Endpoint er uiteindelijk uit ziet, hier volgt een voorbeeld voor deze velden:

Uiteindelijke GET url = https://jouwGatewayDomein/api/barendrecht/betalingen \
Path = `barendrecht/betalingen/{id}` \
Path Regex = `^barendrecht/betalingen/?([a-z0-9-]+)?$`

### Objecten importen via de Common Gateway ###

De Common Gateway ondersteunt het toevoegen van objecten door middel van bijvoorbeeld een Excel bestand. 
Meer informatie over deze functionaliteit kun je [hier](https://commongateway.github.io/CoreBundle/pages/Features/ImportExport) terug vinden.

Via de Gateway UI kun je via -> `Import and upload` bestanden uploaden. Hier onder volgen een aantal punten om op te letten bij het inladen van objecten via een excel bestand:
- Voordat je een excel bestand import, zorg er voor dat in je excel sheet een kolom aanwezig is met een unieke identificatie voor elke regel / object (kan zo simpel zijn als nummering 1,2,3 etc.). 
Voeg de naam van deze kolom toe als Property aan je Schema en aan de mapping die je gebruikt voor importeren:`'_id' : 'naamVanDeKolom'`. 
Hierdoor kan je meerdere keren het zelfde bestand uploaden zonder dat er dubbelen objecten ontstaan.
- Voordat je een excel bestand import, zorg dat je deze hebt opgeslagen met het juiste tabblad open.
Alleen de gegevens uit dit tabblad worden gebruikt voor het importeren van objecten.
- Voor optimale performance/ervaring raden we voor nu aan niet meer dan 300 tot 500 objecten per tabblad tegelijk in te laden (meer is zeker wel mogelijk en kan je ook zeker proberen, maar het kan zijn dat de Gateway UI dit niet zo leuk vind).
- Check altijd na het inladen of alle objecten die je wilde inladen aanwezig zijn, is dit niet het geval. 
Probeer dan nog eens precies dezelfde gegevens in te laden. (of als je weet welke objecten ontbreken kun je ook alleen deze specifieke objecten te selecteren).

## Bijdragen en Ondersteuning
Voor bijdragen en ondersteuning volgen we de spelregels van de Foundation for Public Code. 
Uw bijdragen zijn welkom via onze GitHub repository.

## Licentie
Deze plugin wordt vrijgegeven onder de EUPL 1.2 licentie.