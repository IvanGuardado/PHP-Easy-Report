<table:table table:name="Table1" table:style-name="Table1">
    <table:table-column table:style-name="Table1.A" table:number-columns-repeated="<?php echo count($this->getHeaderRow())?>"/>
        
        <table:table-row>
            <?php foreach($this->getHeaderRow() as $field):?>
            <table:table-cell table:style-name="Table1.A1" office:value-type="string">
                <text:p text:style-name="Table_20_Contents"><?php echo $field?></text:p>
            </table:table-cell>
            <?php endforeach?>
        </table:table-row>
        
        <?php foreach($this->getData() as $row):?>
        <table:table-row>
            <?php foreach($row as $cell):?>
                <table:table-cell table:style-name="Table1.A1" office:value-type="string">
                    <text:p text:style-name="Table_20_Contents"><?php echo $cell?></text:p>
                </table:table-cell>
            <?php endforeach?>
        </table:table-row>
        <?php endforeach?>
</table:table>
