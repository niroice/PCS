<?php
    class InformationPanel
    {
        private $productResults;
        private $transactionResults;
        
        private $claimType;
        
        public function __construct($productResults, $transactionResults)
        {
            $this->transactionResults = $transactionResults;
            $this->productResults = $productResults;  
            
            $this->createInformationFields();
        }
        
        private function createInformationFields()
        {
            echo "
                <fieldset>
                    <legend> More Details </legend>
                    <div class=\"more-details-heading\">Product Details</div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Product Description:</span>
                            <span> " . $this->productResults["ProductName"] . "</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Model Number: </span>
                            <span> " . $this->productResults["Model"] . "</span>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Sell Price:</span>
                            <span>" . $this->productResults["SellPrice"] . "</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Stock On Hand: </span>
                            <span>" . $this->productResults["StockOnHand"] . "</span>
                        </div>
                    </div>

                    <div class=\"line\"></div>

                    <div class=\"more-details-heading\">Supplier Details</div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Supplier ID:</span>
                            <span>" . $this->productResults["SupplierID"] . "</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Phone: </span>
                            <span>" . $this->productResults["SupplierPhone"] . "</span>
                        </div>
                    </div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Name:</span>
                            <span>" . $this->productResults["SupplierName"] . "</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Email: </span>
                            <span>" . $this->productResults["SupplierEmail"] . "</span>
                        </div>
                    </div>
                    <div class=\"input-wrapper\">
                            <span class=\"more-details-label\">Address:</span>
                            <span>" . $this->productResults["SupplierAddress"] . "</span>
                    </div>

                    <div class=\"line\"></div>";
                    
            // if transaction information is provided show the information - empty array means no information
            if (!empty($this->transactionResults))
            {
            echo "

                    <div class=\"more-details-heading\">Purchase Details</div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Purchase Date:</span>
                            <span>" . $this->transactionResults["PurchaseDate"] . "  </span>
                        </div>
                    <div class=\"right-wrapper\">
                        <span class=\"more-details-label\">Purchase Price: </span>
                        <span>$" . $this->transactionResults["PurchasePrice"] . " </span>
                        </div>
                    </div>

                    <div class=\"line\"></div> ";
            echo "
                    <div class=\"more-details-heading\">Claim Details</div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">Raise Claim:</span>
                            <span>" . $this->convertBooleanMessage($this->productResults["Claimable"]). "</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Repairable: </span>
                            <span>" . $this->convertBooleanMessage($this->productResults["Repairable"]). "</span>
                        </div>
                    </div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <span class=\"more-details-label\">ROA Period:</span>
                            <span>" . $this->productResults["WarrantyMonths"] . " months</span>
                        </div>

                        <div class=\"right-wrapper\">
                            <span class=\"more-details-label\">Need RA Number: </span>
                            <span>" . $this->convertBooleanMessage($this->productResults["RARequired"]). "</span>
                        </div>
                    </div>
                    ";
            }
            
            echo "
                    </fieldset>
                            ";
        }
        
        private function convertBooleanMessage($boolean)
        {
            if ($boolean == true)
            {
                return "Yes";
            }
            else
            {
                return "No";
            }
        }
        
    
    }
?>