<?php
$u = App\Models\User::find(1);
echo 'User Company: ' . $u->company_id . "\n";
$p = App\Models\Pos\Product::first();
echo 'Product Company: ' . ($p ? $p->company_id : 'NO PRODUCTS') . "\n";
