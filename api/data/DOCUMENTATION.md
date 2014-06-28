# xPay Payment Service API Dokumentation

Als Shop Entwickler sind lediglich die beiden Endpunkte
[`setCheckout`](#set-checkout) und [`doCheckout`](#do-checkout) interessant.

Als Payment Service Entwickler ist lediglich der Endpunkt
[`doTransaction`](#do-transaction) interessant.

## Ablaufdiagramm
Nachfolgend dargestellt sind die üblichen Prozesse beim durchführen eines
Bezahlvorgangs durchgeführt werden:
![Ablaufdiagramm](/img/workflow.png)

Nachfolgend werden die einzelnen Schritte noch einmal genauer erläutert. Dabei
hebt das folgende Farbschemata hervor:

* Rot: Payment Service -> Shop
* Gelb: Shop -> Payment Service
* Lila: PS -> PS

1. Der `setCheckout` Request informiert xPay über eine anstehende
   Zahlungsabwicklung. Hierbei wird unter anderem der gewünschte Betrag
   übermittelt.
2. xPay sendet daraufhin an den Shop eine `checkout_id` welche die eben
   angefragte Zahlungsabwicklung bestätigt und eindeutig kennzeichnet.
3. Der Shop leitet nun mit Hilfe der `checkout_id` den Kunden auf unsere Seite
   um, der dann die Zahlung bestätigen muss.
4. (Irrelevant für Shop Entwickler)
5. Nach erfolgter Zahlung wird der Kunde wieder zurück zum Shop geschickt. Die
   Weiterleitungsadresse muss im ersten Schritt angegeben werden. Für den Fall
   dass die Zahlung fehlschlägt, kann auf eine andere Adresse weitergeleitet
   werden.
6. Nachdem der Kunde die Zahlung zunächst bestätigt hat, muss die tatsächliche
   Ausführung vom Shop über eine `doCheckout` Anfrage final abgeschlossen werden.
7. (Irrelevant für Shop Entwickler)

## API Anfragen & HTTP-Header
Die API unterstützt zwei primäre Antwortformate: **XML** und **JSON**. Jede
Anfrage an unsere API muss einen entsprechenden *Accept* Header enthalten.
Ist die Antwort in XML gewünscht, so muss der Header `Accept: application/xml`
übermittelt werden, andernfalls `Accept: application/json`.

Für POST Anfragen darf außerdem nicht vergessen werden den korrekten
*Content-Type* Header zu verwenden, da die mitgesendeten Parameter sonst von dem
Webserver nicht erkannt werden können. Falls dies nicht automatisch durch die
verwendete Bibliothek geschieht muss der Header wie folgt angegeben werden:
`Content-Type: application/x-www-form-urlencoded`.

TODO: Auth Header für doTransaction Request beschreiben.

## Datentypen
Zur Validierung der Eingabedaten ist es notwendig, die Datentypen korrekt zu verwenden:

* **string:** Zeichenkette
* **integer:** Natürliche Zahl
* **double:** Reelle Zahl

## Antwort
Eine Antwort besteht aus zwei Teilen:

* **result:** Ergebnis des Requests mit Eingabe- und Rückgabe-Daten
* **error:** Fehler Code und Nachricht

## Fehler
Im folgenden werden die möglichen Error Codes die als Antwort auf einen Request gesendet werden, beschrieben.

<table class="table">
    <tr>
        <th>Code</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>1000</td>
        <td>
            Die Validierung der Eingabedaten war erfolgreich.
        </td>
    </tr>
    <tr>
        <td>1100</td>
        <td>
            Die Validierung der Eingabedaten ist fehlgeschlagen. Genauere Informationen sind der expliziten Nachricht zu entnehmen.
        </td>
    </tr>
    
</table>

## setCheckout<a name="set-checkout"></a>
- **Endpunkt:** http://api.xpay.wsp.lab.sit.cased.de/setCheckout
- **HTTP Verb:** POST
- **Beschreibung:** Legt eine Anforderung an eine Zahlungsabwicklung an. Es wird
  eine eindeutige `checkout_id` generiert welche 24 Stunden ab Beginn der
  Anfrage gültig ist. Sollte der Kunde innerhalb dieser Frist keine
  Zahlungsbestätigung durchführen (durch welche der Checkout erneut nach 24
  Stunden ungültig wird), muss der Shop erneut einen checkout anlegen.
### Eingabe
<table class="table">
    <tr>
        <th>Parameter</th>
        <th>Req./Opt.</th>
        <th>Datentyp</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>type</td>
        <td>required</td>
        <td>integer</td>
        <td>
            Der Typ des Checkouts. Kann folgende Werte annehmen:<br>
            <strong>1</strong> - Falls es sich um eine Bestellung handelt
            (z.B. Wareneinkauf eines Kunden in einem Shop)<br>
            <strong>3</strong> - Falls es sich um eine Rückbuchung handelt.
            In diesem Fall muss als Referenz die entsprechende transaction_id
            angegeben werden.
        </td>
    </tr>  
    <tr>
        <td>receiver_account_number</td>
        <td>required</td>
        <td>integer</td>
        <td>
            Die Kontonummer des Empfängers
        </td>
    </tr>
    <tr>
        <td>amount</td>
        <td>required</td>
        <td>double</td>
        <td>
            Der geforderte Zahlungsbetrag in der angegebenen Währung
            (siehe currency). Beträge mit mehr als zwei Nachkommastellen werden
            auf zwei Nachkommastellen gerundet. Der angegebene Betrag darf
            maximal 999.999,99 betragen - unabhängig von der angegebenen
            Währung. Abhängig von dem Typ der Zahlung muss der Betrag außerdem
            ggf. größer als 0 betragen (siehe type).
        </td>
    </tr>
    <tr>
        <td>description</td>
        <td>required</td>
        <td>string</td>
        <td>
            Der Verwendungszweck der Zahlung. Wird auf dem Kontoauszug
            angezeigt.<br>
            Die Länge ist auf 255 Zeichen begrenzt.
        </td>
    </tr>
    <tr>
        <td>return_url</td>
        <td>required</td>
        <td>string</td>
        <td>
            Die URL auf die der Kunde weitergeleitet werden soll, nachdem die
            Zahlung erfolgreich durch ihn bestätigt wurde.<br>
            Die Länge der URL ist auf 255 Zeichen begrenzt.
        </td>
    </tr>
    <tr>
        <td>cancel_url</td>
        <td>required</td>
        <td>string</td>
        <td>
            Die URL auf die der Kunde weitergeleitet werden soll, falls die
            Zahlungsbestätigung fehlgeschlagen ist. Das kann z.B. der Fall sein
            wenn der Kunde kein ausreichend gedecktes Konto hat, oder die
            Zahlung abbricht.<br>
            Die Länge der URL ist auf 255 Zeichen begrenzt.
        </td>
    </tr>
    <tr>
        <td>currency</td>
        <td>optional</td>
        <td>string</td>
        <td>
            Die Währung in welcher der Zahlungsbetrag angegeben ist. Kann
            entweder <strong>EUR</strong> oder <strong>USD</strong> sein.
            Falls keine Währung angegeben wird, wird automatisch die Währung
            <i>EUR</i> angenommen.
        </td>
    </tr>
    <tr>
        <td>tax</td>
        <td>optional</td>
        <td>double</td>
        <td>
            Falls auf den angegebenen Betrag eine Steuer entfällt, kann hier die
            Steuer in Prozent angegeben Werten. Der Gültigkeitsbereich liegt
            zwischen <strong>0</strong> und <strong>1</strong>
        </td>
    </tr>
    <tr>
        <td>reference</td>
        <td>optional</td>
        <td>string</td>
        <td>
            Eine optionale Referenz die mit dem checkout verknüpft wird. Die
            Referenz erscheint nach erfolgter Zahlung auf dem Kontoauszug
            des Empfängers. Der Inhalt kann beliebig gewählt werden und z.B.
            die Bestellnummer enthalten. Falls es sich um eine Rückbuchung
            handelt muss dieses Feld den Wert der entsprechenden transaction_id
            enthalten.
            Die Länge ist auf 255 Zeichen begrenzt.
        </td>
    </tr>
</table>
### Rückgabe
<table class="table">
    <tr>
        <th>Parameter</th>
        <th>Datentyp</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>checkout_id</td>
        <td>integer</td>
        <td>
           Identifikationsnummer für angeforderte Zahlungsabwicklung
        </td>
    </tr>    
</table>


## doCheckout<a name="do-checkout"></a>
- **Endpunkt:** http://api.xpay.wsp.lab.sit.cased.de/doCheckout
- **HTTP Verb:** POST
- **Beschreibung:** Schließt eine durch den Kunden bestätigte Zahlungsabwicklung ab. Nur gültig innerhalb von 24 nach Anforderung der Zahlungsabwicklung.
### Eingabe
<table class="table">
    <tr>
        <th>Parameter</th>
        <th>Req./Opt.</th>
        <th>Datentyp</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>checkout_id</td>
        <td>required</td>
        <td>string</td>
        <td>
           checkout_id, die bei <strong>setCheckout</strong> zurückgegeben wurde.
        </td>
    </tr>  
</table>
### Rückgabe
<table class="table">
    <tr>
        <th>Parameter</th>
        <th>Datentyp</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>transaction_id</td>
        <td>integer</td>
        <td>
           Identifikationsnummer für die Zahlung. Wird bei Rückbuchung als reference benötigt.
        </td>
    </tr>
    <tr>
        <td>amount</td>
        <td>double</td>
        <td>
            Der in der angeforderten Zahlungsabwicklung ausgewiesen Betrag.
        </td>
    </tr>      
</table>
