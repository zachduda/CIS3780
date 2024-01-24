<?php
require_once("_sql.php");
echo $headers;
?>
<div class="container p-5">
<h1 class="pt-5 text-center no-print">Bidding Sheet</h1>
<p class="no-print">Automatically will fill with Item details. Starting bid will always be half of retail value.</p>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Lot #</th>
      <th scope="col">Item</th>
      <th scope="col">Donated By</th>
      <th scope="col">Retail Val.</th>
      <th scope="col">Starting Bid</th>
    </tr>
  </thead>
<tbody>
<?php

$sql_get = "SELECT * FROM `Item`";
$result = $conn->query($sql_get);

if ($result->rowCount() > 0) {
  	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  		$resultset[]=$row;
  	}
    foreach($resultset as $set){
        $itemid = $set["ItemID"];
        $lot = $set["LotID"];
        $description = $set["Description"];
        $retail = $set["RetailValue"];
        $donatedby = $set["DonorID"];
?>
        <tr>
          <th scope="row"><?php echo $lot;?></th>
          <td><?php echo $description;?></td>
          <td><?php echo $donatedby;?></td>
          <td>$<?php echo number_format($retail, 2);?></td>
          <td>$<?php echo number_format($retail/2, 2);?></td>
        </tr>
<?php
    }
}
?>
</tbody>
</table>
<br class="no-print">
<div class="p-4" style="page-break-before: always;">
<h2>Submit Your Bid:</h2>
<p>Print out this document and fill in the Lot #, Item Description, and your Bidder ID number with your bid.</p>
<br class="only-print">
<table class="table">
  <thead>
    <tr>
      <th scope="col">Lot #</th>
      <th scope="col">Item</th>
      <th scope="col">Bidder ID</th>
      <th scope="col">Bid</th>
    </tr>
  </thead>
<tbody>
    <tr>
      <th scope="row" class="py-5">_________</th>
      <td class="py-5">______________________________</td>
      <td class="py-5">_________</td>
      <td class="py-5">_________</td>
    </tr>
</tbody>
</table>
</div>
</div>
<?php echo $js; ?>
