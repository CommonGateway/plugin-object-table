# Objecten Tabellen
Het publiceren van dynamische tabellen in WordPress aan de hand van de Overige Objecten Standaard van de VNG (Vereniging van Nederlandse Gemeenten) stelt gemeenten in staat om eenvoudig en efficiënt gegevens uit hun datawarehouse te publiceren op hun websites. Door gebruik te maken van deze standaard, kunnen gemeenten gestructureerde data naadloos integreren in hun WordPress-sites, waarbij de gegevens dynamisch worden bijgewerkt om actuele en accurate informatie weer te geven. Dit proces automatiseert de dataflow, vermindert de kans op fouten, en zorgt voor consistentie in de gegevenspresentatie, waardoor burgers toegang krijgen tot betrouwbare en up-to-date informatie.

Deze aanpak biedt aanzienlijke voordelen voor zowel gemeenten als burgers. Voor gemeenten betekent het een vereenvoudiging van het beheer van online content, waarbij tijdrovende handmatige updates worden geëlimineerd. Burgers profiteren van een verbeterde toegang tot informatie, waardoor de transparantie en de dienstverlening van de gemeente worden verhoogd. Door het gebruik van dynamische tabellen kunnen gegevens zoals gemeentelijke voorzieningen, projectupdates, statistieken, en meer, direct en op een gebruiksvriendelijke manier worden gepresenteerd, wat bijdraagt aan een beter geïnformeerde en betrokken gemeenschap.

## Hoe te gebruiken
1. Upload de plugin .zip op de Plugins-pagina.
2. Activeer de plugin.
3. Ga naar Instellingen -> ObjectTabel-pagina.
4. Voeg een configuratie toe met API URL en API KEY.
5. U kunt ervoor kiezen om een CSS-klasse toe te voegen aan de tabel als er een is.
6. U kunt kiezen om een optionele mapping JSON toe te voegen, bijvoorbeeld {"name": "naam"}. Een mapping kan handig zijn als je minder data per object wilt tonen zoals alleen de naam, of bijvoorbeeld helemaal andere keys in de tabel header wilt tonen dan hoe het oorspronkelijk in het object staat.
7. Voeg een shortcode toe aan een gewenste pagina zoals: [object-tabel configId="2"], waarbij configId de id is van een van uw configuraties op de instellingenpagina.
8. Bij het bezoeken van de pagina zal de shortcode code activeren die de gegevens ophaalt en een tabel weergeeft.

## Afhankelijkheden
Deze plugin vereist een Overige Objecten Registratie als enige afhankelijkheid om effectief te kunnen functioneren. Een Overige Objecten Registratie stelt de plugin in staat om gestructureerde data te verkrijgen en te presenteren in dynamische tabellen op uw WordPress-site. Voor het optimaal benutten van deze afhankelijkheid, adviseren wij het gebruik van een Common Gateway.

Een Common Gateway faciliteert de naadloze integratie van data uit verschillende bronnen, inclusief Overige Objecten Registraties, door een uniforme toegangspoort te bieden. Het stelt de plugin in staat om data dynamisch te verkrijgen en bij te werken, wat essentieel is voor het accuraat en actueel weergeven van informatie in de tabellen. De gateway zorgt voor een gestroomlijnde en beveiligde dataflow, wat bijdraagt aan de betrouwbaarheid en efficiëntie van de plugin.

## Bijdragen en Ondersteuning
Voor bijdragen en ondersteuning volgen we de spelregels van de Foundation for Public Code. Uw bijdragen zijn welkom via onze GitHub repository.

## Licentie
Deze plugin wordt vrijgegeven onder de EUPL 1.2 licentie.
