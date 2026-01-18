<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
    <html>
    <head>
        <title>Raport Zakupów</title>
        <style>
            table {border-collapse: collapse; width: 100%;}
            th, td {border: 1px solid black; padding: 8px; text-align: left;}
            th {background-color: #f2f2f2;}
        </style>
    </head>
    <body>
        <h2>Lista wszystkich transakcji</h2>
        <table>
            <tr>
                <th>Data</th>
                <th>Klient</th>
                <th>Towar</th>
                <th>Cena jedn.</th>
                <th>Ilość</th>
            </tr>
            <xsl:for-each select="sklep/zakupy/zakup">
                <tr>
                    <td><xsl:value-of select="data"/></td>
                    <td>
                        <xsl:value-of select="id(@klient_ref)/imie"/>&#160;
                        <xsl:value-of select="id(@klient_ref)/nazwisko"/>
                    </td>
                    <td><xsl:value-of select="id(@towar_ref)/nazwa"/></td>
                    <td><xsl:value-of select="id(@towar_ref)/cena"/> PLN</td>
                    <td><xsl:value-of select="ilosc"/></td>
                </tr>
            </xsl:for-each>
        </table>
    </body>
    </html>
</xsl:template>
</xsl:stylesheet>