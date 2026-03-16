import React from "react";

export default function GameTipsContent() {

    return (
        <>
            <h1>Typy a vyhodnocování sázek</h1>
            <h2>Základní typ sportovních sázek (SOLO)</h2>
            <p>Jednoduché neboli <strong>solo sázky</strong> znamenají, že tipujete <strong>jednu konkrétní sázkovou příležitost</strong> – například vítěze zápasu, počet gólů nebo bodový rozdíl.</p>
            <hr />

            <h2> 1X2 (Three-way, výhra/remíza/prohra)</h2>
            <p>Klasická sázka s <strong>třemi možnými výsledky</strong>, nejčastější u fotbalu, hokeje nebo házené.</p>
            <p>Sázíte na výsledek po <strong>základní hrací době</strong> – tedy bez prodloužení a penalt.</p>
            <ul>
                <li><strong>1 = výhra domácího týmu</strong></li>
                <li><strong>X = remíza</strong></li>
                <li><strong>2 = výhra hostujícího týmu</strong></li>
            </ul>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-football icon-width-footer" ></i>Příklad (fotbal):</h3>
            <p>Zápas: Sparta Praha – Slavia Praha</p>
            <ul>
                <li>Výsledek 2:1 → výhra sázky „domácí“</li>
                <li>Výsledek 1:1 → výhra sázky „remíza“</li>
                <li>Výsledek 1:2 → výhra sázky „hosté“</li>
            </ul>
            <p>V hokeji se obdobně vyhodnocuje po 60 minutách hry. Pokud zápas skončí remízou a jde do prodloužení, sázka 1X2 je už rozhodnutá po základní době.</p>
            <hr />
            <h2>Home/Away (Domácí/Hosté)</h2>
            <p>Zjednodušená verze sázky 1X2 – remíza zde <strong>neexistuje</strong>.</p>
            <p>Vítěz se počítá <strong>včetně prodloužení, nájezdů, tie-breaku nebo jiné formy rozhodnutí</strong>.</p>
            <ul>
                <li><strong>Home = domácí tým vyhraje (včetně prodloužení či nájezdů)</strong></li>
                <li><strong>Away = hostující tým vyhraje (včetně prodloužení či nájezdů)</strong></li>
            </ul>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-hockey icon-width-footer" ></i>Příklad (hokej – NHL):</h3>
            <p>Zápas: Boston Bruins – Toronto Maple Leafs</p>
            <p>Sázka: „Home“ → vyhrává, pokud Boston zvítězí i po nájezdech.</p>
            <h4>Poznámka:</h4>
            <p>Pokud zápas skončí <strong>nerozhodně a nenásleduje žádné prodloužení ani jiné rozhodnutí</strong>, které by určilo vítěze (např. v základní části turnaje), <strong>sázka se vrací s kurzem 1,00</strong>.</p>
            <p>Tedy dostanete zpět svůj vklad – ani výhra, ani prohra.</p>
            <hr />

            <h2>Handicap (Hendikep)</h2>
            <p>Virtuální <strong>náskok nebo ztráta</strong>, která se přičítá ke skutečnému výsledku</p>
            <hr />

             <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-football icon-width-footer" ></i>Příklad (fotbal):</h3>
            <p>Sázka: tým Sparta (domácí) handicap -1,5</p>
            <ul>
                <li>Výsledek 3:1 → po odečtení 1,5 gólu je výsledek 1,5:1 → <strong>výhra sázky</strong></li>
                <li>Výsledek 2:1 → po odečtení 1,5 gólu je 0,5:1 → <strong>prohra sázky</strong></li>
            </ul>

             <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-basketball icon-width-footer" ></i>Příklad (basketbal):</h3>
            <p>Sázka: tým B (hosté) +7,5 → tým B může prohrát až o 7 bodů a sázka stále vyhrává.</p>
            <hr />

            <h2>Počet bodů/gólů (Over/Under)</h2>
            <p>Sázíte na <strong>celkový počet bodů nebo gólů v zápase</strong>, bez ohledu na vítěze.Základní forma:.</p>
            <ul>
                <li><strong>Over (více než)</strong> – padne více než daný počet</li>
                <li><strong>Under (méně než)</strong> – padne méně než daný počet</li>
            </ul>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-basketball icon-width-footer" ></i>Příklad (basketbal):</h3>
            <p>Sázka: Over 205,5 bodu</p>
            <ul>
                <li>Výsledek 108:104 → 212 bodů → <strong>výhra</strong></li>
                <li>Výsledek 99:102 → 201 bodů → <strong>prohra</strong></li>
            </ul>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-football icon-width-footer" ></i>Příklad (fotbal):</h3>
            <p>Sázka: Under 2,5 gólu</p>
            <ul>
                <li>Výsledek 1:1 → 2 góly → <strong>výhra</strong></li>
                <li>Výsledek 2:1 → 3 góly → <strong>prohra</strong></li>
            </ul>
            <p>Používá se i v hokeji (počet gólů), baseballu (počet hitů/runů), americkém fotbalu (součet bodů), ragby, volejbale nebo házené.</p>
            <hr />

            <h2><i className="icon sport-icon sbc-icon-sport-baseball icon-width-footer" ></i>Baseball – Počet hitů / runů</h2>
            <p>Speciální trh pro baseball: sázíte na <strong>celkový počet hitů (úspěšných odpálení)</strong> nebo <strong>runů (bodů)</strong> v zápase.</p>
            <ul>
                <li>Výsledek 1:1 → 2 góly → výhra</li>
                <li>Výsledek 2:1 → 3 góly → prohra</li>
            </ul>

            <h3>Příklad:</h3>
            <p>Sázka: Over 18,5 hitů (odpalů)</p>
            <ul>
                <li>Zápas skončí s 19 a více odpaly → <strong>výhra</strong></li>
                <li>18 nebo méně odpalů → <strong>prohra</strong></li>
            </ul>
            <hr />

            <h2>Body týmu</h2>
            <p>Sázíte na <strong>počet bodů konkrétního týmu</strong> – například kolik gólů vstřelí nebo kolik bodů získá.</p>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-nfl icon-width-footer" ></i>Příklad (americký fotbal):</h3>
            <p>Sázka: Kansas City Chiefs Over 27,5 bodu</p>
            <ul>
                <li>Výsledek 31:28 → tým dal 31 bodů → <strong>výhra</strong></li>
                <li>Výsledek 31:28 → tým dal 31 bodů → <strong>výhra</strong></li>
            </ul>
            <p>Tento typ sázky se používá také u basketbalu, házené nebo volejbalu (např. „Tým A dá více než 90 bodů“).</p>

            <hr />

            <h2> Result/Total (Výsledek + Počet bodů)</h2>
            <p>Kombinovaná sázka, kde tipujete <strong>vítěze i celkový počet bodů/gólů</strong> v jednom tipu.Obě části musí být správné.</p>

            <h3 className="center-h"><i className="icon sport-icon sbc-icon-sport-football icon-width-footer" ></i>Příklad (fotbal):</h3>
            <p>Sázka: 1 & Over 2,5</p>
            <ul>
                <li>Výsledek 3:1 → výhra (domácí vyhráli a padly 3+ góly)</li>
                <li>Výsledek 1:0 → prohra (málo gólů)</li>
                <li></li>
            </ul>

            <hr />

            <h2>Shrnutí: jak číst kurz a vyhodnotit sázku</h2>
            <p>Každá příležitost má <strong>kurz</strong> (např. 1.80, 2.50, 3.10).</p>
            <ul>
                <li>Kurz 2.00 = při sázce 100 Kč vyhrajete 200 Kč (zisk 100 Kč).</li>
                <li>Pokud sázka nevyjde, přicházíte o vklad.</li>
                <li>Pokud je sázka <strong>vrácena (kurz 1,00)</strong>, dostanete zpět celý vklad.</li>
            </ul>
            <p>SOLO sázky jsou tedy <strong>nejjednodušší formou kurzového sázení</strong>, protože obsahují pouze <strong>jeden výsledek jedné události</strong>.</p>
            <hr />

            <h2>Nejčastější typy sázek podle sportu</h2>

            <table>
                <thead>
                    <th><strong>Sport</strong></th>
                    <th><strong>Typické sázky</strong></th>
                    <th><strong>Poznámky</strong></th>
                </thead>
                <tbody>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-football icon-width-footer-small" ></i> Fotbal</strong></td>
                        <td>1X2, Handicap, Over/Under, Result/Total</td>
                        <td>Vyhodnocuje se po 90 min. bez penalt</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-hockey icon-width-footer-small" ></i> Hokej</strong></td>
                        <td>1X2, Home/Away, Over/Under, Handicap</td>
                        <td>1X2, over/under, handicap – vyhodnocuje se  po 60 min; Home/Away = včetně nájezdů</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-basketball icon-width-footer-small" ></i> Basketbal</strong></td>
                        <td>Home/Away, Handicap, Over/Under, Body týmu</td>
                        <td>Žádná remíza, počítá se prodloužení</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-nfl icon-width-footer-small" ></i> Americký fotbal</strong></td>
                        <td>Home/Away, Body týmu, Handicap, Result/Total</td>
                        <td>Zahrnuje prodloužení</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-baseball icon-width-footer-small" ></i> Baseball</strong></td>
                        <td>Home/Away, Over/Under (runy, hity), Handicap</td>
                        <td>Žádná remíza, počítají se extra runy</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-handball icon-width-footer-small" ></i> Házená</strong></td>
                        <td>1X2, Over/Under, Handicap</td>
                        <td>Výsledek po 60 minutách</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-volleyball icon-width-footer-small" ></i> Volejbal</strong></td>
                        <td>Home/Away, Handicap, Počet setů</td>
                        <td>Počítá se vítěz zápasu nebo konkrétní set</td>
                    </tr>
                    <tr>
                        <td><strong className="center-h"><i className="icon sport-icon sbc-icon-sport-rugby icon-width-footer-small" ></i> Rugby</strong></td>
                        <td>Home/Away, Handicap, Over/Under</td>
                        <td>Všechny sázky se počítají v základní hrací době</td>
                    </tr>
                </tbody>
            </table>
        </>
    );
}
