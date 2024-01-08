<?php

// 1. PHP(dělal jsem v laravelu, nette ale nedokážu říct že je ovládám)
//    MySQL, PostreSQK, MongoDB, GraphSQL, Firebase
//    Tailwind, Bootstrap, scss,
//    React, Next.js, dělal jsem jednou v react native  pro mobilní aplikace      ale   netvrdil bych že ho ovládám
// 2. Xamp, Visual Studio Code
// 3. Stavební s nástavbou, kurzy online ale bez certifikátu
// 4. Videa na youtube, docs z offical stránek, overflow, chatgpt
// 5. Před rokem jsem začínal teprve s html a css tak není moc z čeho hodnotit
// jediný co bych řekl asi že zapomínám hodně komentovat kód což mi zhoršuje přehlednost v něm
// 6. Momentálně nemám. Snažím se každým projektem naučit něco nového a zlepšit se bohužel občas zapomenu na něco co jsem dělal před rokem když stím nedělám pořád
// 7. Jednou jsem Dával jednu appku co měla backend na Forpsi asi kde použivají Marian DB ale jediný co jsem vlastně dělal že jsem nahrál projekt na server po změnil connect k db a importoval databázi z phpmyadmin do marianDB nic složitého.
// - moje zkušenosti s Linuxem jsou minamální jediné moje zkušenosti s linuxem jsou s Kali Linuxem kde jsem si zkoušel hackovat hesla a zkoušel jsem různé typy útoků SQL Injection DDOS atd.. 
// 8. Bohužel kód a testování mám zkušenosti minimální a nedělal jsem testování kódu z ničím
// 9. I believe my English skills are at a B2 level, but I'm not entirely certain. I can read and understand everything well. However, since I don't speak every day, I'm unsure about my speaking skills. Nonetheless, I feel confident in my reading and writing abilities.
// 10. Chtěl bych říct že v minulosti už když jsme měli svůj vlastní Minecraft server, který byl pořád plný tenkrát mi bylo 14-15 let já se staral o věci jako jsou pluginy a webovou stránku, tenkrát se pluginy psali v javascrpitu a já je updatoval a upravoval aniž bych věděl co javascript je. Pak jsem do 25 let vlastně na nic nešahal a teďka poslední rok se snažím hodně programovat a učit se nové věci a zlepšovat se.
// Momentálně mě nejvíc baví asi technologie jako je React nebo Next.js a která by mě nejvíc bavila nemám tušení
// 11. Programování, hry, filmy / seriály, gym, cestování


/////////////////////// PHP ///////////////////////

namespace App\Repository;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
//use App\Repository\Debugger;

class GWLNumberRepository
{
    private $cache;
    private $cacheKey = 'stats';
    private $data;

    public function __construct(
        IStorage $storage
    ) {
        $this->cache = new Cache($storage, 'gwl-stats');
        $this->data = $this->getData();
    }

    /**
     * Gets data from GWL Web Service
     * @return mixed false if data is not valid, data otherwise
     */
    public function fetchDataFromWS()
    {
        try {
          $input = file_get_contents('https://www.ev-power.eu/ws/gwlsalesstatout.php');
        $json = json_decode($input);
    
          // check if data are valid
          if (!$json->bWebServiceCon) {
          return false;
          }
        
       
        return $json;
      } catch (\Throwable $e) {
        // Handle the exception (log it, rethrow it, etc.)
        return false;
    }
}

    public function getData() {
        $cachedData = $this->cache->load($this->cacheKey);

        if ($cachedData !== null) {
            return $cachedData;
        }

        try {
          $serverData = $this->fetchDataFromWS();
        } catch (GWLDashboardException $e) {
          Debugger::log($e);
          return null;
        }
       
        if ($serverData) {
            $this->cache->save($this->cacheKey, $serverData, [
                Cache::EXPIRE => '1 second' // tady to nevím no.. asi bych prodloužil čas nebo bych napsal CACHE_EXPIRATION_SECONDS a nahoře udělal constatnu kde bych označil ACHE_EXPIRATION_SECONDS = 10 seconds a měnil tam ? nevím 
            ]);

            return $serverData;
        }
    }

    public function getThisYrCustomers()
    {
        return $this->data->iSatisfiedCustAct;
    }

    public function getThisYrOrders()
    {
        return $this->data->iShippedOrdAct;
    }

    public function getThisYrKWhsCapacity()
    {
        return round($this->data->dWhsCapAct);
    }

    public function getThisYrKgBatteries()
    {
        return round($this->data->dKLFPBatAct);
    }

    /*
    public function getThisYrTonsBatteries()
    {
        return round($this->data->dKLFPBatAct / 1000);
    }
    */

    public function getSince2008Customers()
    {
        return (floor($this->data->iSatisfiedCust2008 / 1000) * 1000).'+';
    }

    public function getSince2008Orders()
    {
        return (floor($this->data->iShippedOrd2008 / 1000) * 1000).'+';
    }

    public function getSince2008KWhsCapacity()
    {
        return (floor($this->data->dWhsCap2008 / 1000) * 1000).'+';
    }

    public function getSince2008KgBatteries()
    {
        return round($this->data->dKgLFPBat2008 / 1000000, 1).' mil.';
    }
}

// asi bych upravil pár bloků do try a catch jinak moc netuším co bych mohl vyle pšit


/////////////////////// GIT ///////////////////////

// 1. s gitem mám žkušenosti ale nikdy jsem moc nepracoval v teamu a nedokážu určit co je špatně a proč nebo tomu schéma moc nerozumím možná ten merge brnch master? vždy se pushuje main a ne master nebo ?
// 2. Použijem firemní servery na kterých máme live napojenný projekt, buď to přehodíš přes git nebo FTP
// 3. netuším vyřešit to nějak nejlejhčí a nejrychlejší cestou, který by co nejmíň zasahovalo do kódu ale nevím jak, nejspíš najéct na branch kde všechno jelo v pořádku ?

/////////////////////// Regulární příkazy ///////////////////////
// použít něco takovéhlo? \b\+\d{1,3}(\s?(\d{3}\s?)){1,3}\d{3}\b
// upřímně nejsem si moc jistý tohle jsem nikdy nepoužíval


/////////////////////// Databáze ///////////////////////
// Tabulka Products:
// product_id (Primární klíč)
// product_name (Název produktu)
// product_description (Popis produktu)
// product_price (Cena produktu)
// product_stock_quantity (Dostupné množství)
// product_created_at (Datum vytvoření)
// product_updated_at (Datum poslední aktualizace)

// Tabulka ProductAttributes:
// attribute_id (Primární klíč)
// product_id (Cizí klíč k tabulce Products)
// attribute_name (Název atributu, např. "Velikost", "Barva")
// attribute_value (Hodnota atributu, např. "M", "L", "Red", "Blue")

// Tabulka Orders:
// order_id (Primární klíč)
// product_id (Cizí klíč k tabulce Products)
// quantity (Počet kusů)
// total_price (Celková cena za položky v objednávce)
// created_at (Datum vytvoření objednávky)

// teď jde o to jestli člověk co může nakouput musí mít účet nebo ne ? 
// Pro podporu budoucího rozvoje můžeš vytvořit další tabulku, např. AdditionalAttributes, která bude obsahovat další parametry produktu.

// 1. Může to mít více variant řešení? Určitě všechno v IT má více řešení
// 2. Jak řešit, aby se po kliku na barevnou variantu změnily obrázky za odpovídající? pomocí javascriptu ? to mi příjde nejlehčí
// 3. Jak mazat produkty? tak to půjde více způsobama mě příjde nejlepší udělat vedle proste cms admin  panel nebo admin dashboard kde bude jak edit tak i mazaní je to asi nejvíc efektivnější a rychlejší způsob jak se hrabat v databázi

// 4. Přidat sloupec availability do tabulky Products, který bude indikovat dostupnost a když bude 0, tak button objednat nebude aktivní. 

/////////////////////// Algoritmy ///////////////////////
// SELECT COUNT(*)
// FROM (
//     SELECT *
//     FROM products
//     WHERE
//         [gender] = [hodnota]
//         [size] = [hodnota]
//         AND price >= [price_min]
//         AND price <= [price_max]
// ) AS temp
// časová složitost je lineární ale bude to muset projít celou databázi několikrát  tím pádem to projede 9x checkbox takže časovou složitost netuším asi bude záviset i na databázi a na tom jak moc je velká