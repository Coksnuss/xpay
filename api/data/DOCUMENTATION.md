# xPay Payment Service API Dokumentation

Als Shop Entwickler sind lediglich die beiden Endpunkte
[`setCheckout`](#set-checkout) und [`doCheckout`](#do-checkout) interessant.

Als Payment Service Entwickler ist lediglich der Endpunkt
[`doTransaction`](#do-transaction) interessant.

## Ablaufdiagramm
Nachfolgend dargestellt sind die üblichen Prozesse beim durchführen eines
Bezahlvorgangs durchgeführt werden:
![Ablaufdiagramm](/img/workflow.png)

Nachfolgend werden die einzelnen Schritte noch einmal genauer erläutert.
Viele Schritte laufen transparent für den Nutzer unserer Schnittstelle ab, daher
wurde gesondert vorgehoben welche zwei Parteien bei dem jeweiligen Schritt
miteinander interagieren, falls zutreffend.

1. <b>Shop -> Payment Service:</b><br>
   Der `setCheckout` Request informiert xPay über eine anstehende
   Zahlungsabwicklung. Hierbei wird unter anderem der gewünschte Betrag
   übermittelt.
2. <b>Payment Service -> Shop:</b><br>
   xPay sendet daraufhin an den Shop eine `checkout_id` welche die eben
   angefragte Zahlungsabwicklung bestätigt und eindeutig kennzeichnet.
3. Der Shop leitet nun mit Hilfe der `checkout_id` den Kunden auf unsere Seite
   um, der dann die Zahlung bestätigen muss.
4. Unser Dienst kümmert sich um die Abwicklung der Zahlungsbestätigung
5. Nach erfolgter Zahlung wird der Kunde wieder zurück zum Shop geschickt. Die
   Weiterleitungsadresse muss im ersten Schritt angegeben werden. Für den Fall
   dass die Zahlung fehlschlägt, kann auf eine andere Adresse weitergeleitet
   werden.
6. <b>Shop -> Payment Service:</b><br>
   Nachdem der Kunde die Zahlung zunächst bestätigt hat, muss die tatsächliche
   Ausführung vom Shop über eine `doCheckout` Anfrage final abgeschlossen
   werden.
7. <b>Payment Service -> Payment Service:</b><br>
   Für den Fall dass der Zahlungsempfänger (=Auftraggeber) sein eigenes Konto
   nicht bei unserem Dienst hat, involvieren wir für die Zahlung den
   entsprechenden Dienstleister.
8. <b>Payment Service -> Payment Service:</b><br>
   xPay wertet die Antwort des anderen Dienstleisters aus. Insbesondere stellt
   unser Dienst in diesem Schritt fest ob die Zahlung erfolgreich war.
9. <b>Payment Service -> Shop:</b><br>
   Sofern die Zahlung erfolgt ist, liefern wir eine TransaktionsID zurück. Diese
   wird im Falle einer Rückbuchung benötigt und dient außerdem als Referenz für
   die erfolgte Zahlung.

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

### Für Betreiber eines Payment Systems...
... ist bei Aufruf von `doTransaction` außerdem ein Authentifizierungs Header
mitzusenden. Zunächst muss dazu ein Konto bei
[xPay](https://xpay.wsp.lab.sit.cased.de) angelegt werden, und anschließend die
Freischaltung (mündlich, via E-Mail oder über PN in Moodle) für die Nutzung der
Methode beantragt werden. Nach erfolgter Freischaltung kann in der
Benutzerübersicht ein API Key generiert werden. Der Aufruf der Schnittstelle
muss über eine
[HTTP Basic Authentication](http://de.wikipedia.org/wiki/HTTP-Authentifizierung#Basic_Authentication)
erfolgen, bei der als Benutzername der API Key angegeben werden muss. Das
Passwort Feld wird ignoriert und darf einen beliebigen Wert enthalten. Als Realm
muss der Wert <b>api</b> angegeben werden.

## Datentypen
Zur Validierung der Eingabedaten ist es notwendig, die Datentypen korrekt zu verwenden:

* **string:** Text mit maximal 255 Zeichen.
* **integer:** Eine ganze, positive Zahl, ohne Nachkommastellen.
* **double:** Gleitkommazahl mit bis zu zwei Nachkommastellen. Enthält die Zahl
mehr als zwei Nachkommestellen wird eine kaufmännische Rundung vorgenommen.

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
- **Endpunkt:** https://api.xpay.wsp.lab.sit.cased.de/setCheckout
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
- **Endpunkt:** https://api.xpay.wsp.lab.sit.cased.de/doCheckout
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


## doTransaction<a name="do-transaction"></a>
- **Endpunkt:** https://api.xpay.wsp.lab.sit.cased.de/doTransaction
- **HTTP Verb:** POST
- **Beschreibung:** Führt eine Einzahlung auf einem Konto aus.
### Eingabe
<table class="table">
    <tr>
        <th>Parameter</th>
        <th>Req./Opt.</th>
        <th>Datentyp</th>
        <th>Beschreibung</th>
    </tr>
    <tr>
        <td>uuid</td>
        <td>required</td>
        <td>string</td>
        <td>
           Eine zufällig, jedoch eindeutig auf die Einzahlung bezogene, UUID im
           allgemein gültigen Format (siehe z.B. http://www.uuidgenerator.net).
           Diese dient dazu eine Einzahlung nicht versehentlich mehrfach
           auszuführen.
        </td>
    </tr>
    <tr>
        <td>sender_account_number</td>
        <td>required</td>
        <td>integer</td>
        <td>
           Die Kontonummer desjenigen von dem der angegebene Betrag ausgeht.
        </td>
    </tr>
    <tr>
        <td>receiver_account_number</td>
        <td>required</td>
        <td>integer</td>
        <td>
           Die Kontonummer des Empfängers, dessen Konto bei xPay liegt.
        </td>
    </tr>
    <tr>
        <td>amount</td>
        <td>required</td>
        <td>double</td>
        <td>
           Der einzuzahlende Wert. Darf nicht negativ sein!
        </td>
    </tr>
    <tr>
        <td>currency</td>
        <td>optional</td>
        <td>string</td>
        <td>
            <strong>Achtung: </strong> Der Zahlungsbetrag muss in EUR angegeben
            sein! Durch die schwankenen Umrechnungskurse müssen alle
            Einzahlungen in der Hauptwährung durchgeführt werden. Dadurch wird
            sichergestellt dass Sender und Empfänger den gleichen Betrag
            abgebucht (bzw. zugebucht) bekommen.
        </td>
    </tr>
    <tr>
        <td>description</td>
        <td>required</td>
        <td>string</td>
        <td>
            Der Einzahlungsgrund bzw. Verwendungszweck.
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
           Identifikationsnummer für die Einzahlung.
        </td>
    </tr>
    <tr>
        <td>amount</td>
        <td>double</td>
        <td>
            Der in der angeforderten Zahlungsabwicklung ausgewiesene Betrag.
        </td>
    </tr>
</table>
