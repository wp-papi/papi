## Ett PageTypeBuilder för WordPress

Idé till hur man skulle kunna bygga ett page type builder i WordPress ovanpå de de vanliga sidorna och egna custom post types.

Genom att läga till en meta box på de vanliga sidorna där man kan välja sidmall så kan vi styra vilka fält som ska visas för redaktören.

![](https://photos-6.dropbox.com/t/0/AAB6AJd6Tl6ffNzd2TDrubsignsnCirgsoMCcGvmjXGJFg/12/4660032/png/1024x768/3/1381089600/0/2/Screenshot%202013-10-06%2020.10.57.png/rcrkWIQK7yvzNiuDx7reSjf_VmFsqxJN6Lsi8MVD6Sg)

*Radio knappar är inte jätte bra om man har många sidmallar.*

När man klickar på `Om sida` så lägger vi på en querystring i urlen `pagetype=about-page` som styr att vi vill lägga till meta boxar som gäller. Troligen lär de inte hända något med själva sidan man skapar när man väljer sidmall, förutom att den auto sparar sidan.

WordPress har inbyggt så man kan välja vilken `page-{x}.php` den ska använda, men i och med att man väljer sidmall så bör vi bortse från denna funktionalitet eftersom de blir dubbelarbete för redaktören.

Det blir lätt många meta boxar på en Custom Post Types eller en vanlig sida med få fält i. Detta borde man arbeta bort och ha en meta box som kan hantera tabbar med fält i. WordPress pluginet `WooCommerce` tycker jag har en bra lösning på detta.

![](https://photos-2.dropbox.com/t/0/AAB5KqFUeEGazyImOCyxyLowplOy0GJnAaHw9PyAr58b9w/12/4660032/png/2048x1536/3/1381089600/0/2/Screenshot%202013-10-06%2020.39.54.png/qu-LDHD0PUoMy8wtN20eQr1rZbA77n67TlF8husDH5U)

### Custom Post Types

Custom Post Types är en bra idé men i praktiken så fungerar dom lite dåligt när de kommer till URL strukturen. Det har vi på Isotop löst med att man skapar upp en vanlig sida och länkar in en custom post type där själva datan för sidan finns.

Custom Post Types kan användas till datasidor eller speciella sidor så som ett köpflöde eller något annat. Man skulle kunna bygga in stöd för flera olika sidmallar för en Custom Post Types, men det kanske inte behövs i alla fallen.

De filerna WordPress använder för Custom Post Types är `single-{x}.php`. Detta behöver man inte ändra på, då de fungerar bra idag.

### Trädvy

För enklare sidor skulle denna idéen med PageTypeBuilder fungerar men för mer anvancerade sidor skulle man behöva bygga en trädvy som klarar av både vanliga sidor, vanliga blogginlägg och Custom Post Types.

### Sidreferenser

I EPiServer finns det bra stöd för att referera till olika sidor. En liknande lösning för WordPress skulle behövas istället för att man har en textruta där man skriver in ett sidid som kan vara svårt att hitta om man inte skriver ut själva id:t någonstans. (Så klart finns det i urlen, men de uppfattas inte av alla).

Där kommer trädvyn in i bilden igen, man behöver lista alla sidor på ett enkelt sätt så som i EPiServer. WordPress pluginet `CMS Tree Page View` stödjer olika trädvyer för olika Custom Post Types, vilket man skulle kunna använda för att filtrera för att få fram rätt sida man vill referera in.

### Kodmässigt

Hur man skulle skapa en ny sidmall kodmässigt är inte riktigt genomtänkt än. Det ska motverka onödig kod och det ska vara enkelt att skapa en ny sida.
