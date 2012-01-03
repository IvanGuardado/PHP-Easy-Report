<table:table table:name="Table1" table:style-name="Table1">
    <table:table-column table:style-name="Table1.A" table:number-columns-repeated="<?php echo count($this->getHeaderRow())?>"/>
        
        <table:table-row>
            <?foreach($this->getHeaderRow() as $field):?>
            <table:table-cell table:style-name="Table1.A1" office:value-type="string">
                <text:p text:style-name="Table_20_Contents"><?php echo $field?></text:p>
            </table:table-cell>
            <?endforeach?>
        </table:table-row>
        
        <?foreach($this->getData() as $row):?>
        <table:table-row>
            <?foreach($row as $cell):?>
                <table:table-cell table:style-name="Table1.A1" office:value-type="string">
                    <text:p text:style-name="Table_20_Contents"><?php echo $cell?></text:p>
                </table:table-cell>
            <?endforeach?>
        </table:table-row>
        <?endforeach?>
</table:table>
