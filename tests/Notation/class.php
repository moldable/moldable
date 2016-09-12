<?php

//
require_once __DIR__.'/../_common.php';

//
$notations = [

    '<<Class>>',
    '<<Class*>>',
    '<<Class**>>',

    '<<Class lookupField>>',
    '<<Class lookupField*>>',
    '<<Class lookupField**>>',

    '<<Name\\Class>>',
    '<<Name\\Space\\Class*>>',
    '<<Name\\Space\\Class**>>',

    '<<Name\\Space\\Class lookupField>>',
    '<<Name\\Space\\Class lookupField*>>',
    '<<Name\\Space\\Class lookupField**>>',

    '<<Name\\Space\\Class lookupField secondLookupField>>',
    '<<Name\\Space\\Class lookupField secondLookupField*>>',
    '<<Name\\Space\\Class lookupField secondLookupField**>>',
];

//
$parser = new Javanile\SchemaDB\Parser\Mysql();

?>

<table border=1 cellpadding=4>
    <tr>
        <th>Notation</th>
        <th>Value</th>
        <th>Type</th>
        <th>Column</th>
    </tr>
    
    <?php foreach ($notations as $notation) {

        $aspects = $parser->getNotationAttributes($notation);
        
        ?>

        <tr>
            <td>
                <pre><?php var_dump(htmlentities($notation)); ?></pre>
            </td>
            <td align="center">
                <?=htmlentities($parser->getNotationValue($notation))?>
            </td>
            <td align="center">
                <?=$parser->getNotationType($notation)?>
            </td>
            <td>
                <pre><?php var_dump($aspects); ?></pre>
            </td>
        </tr>
    <?php } ?>
</table>