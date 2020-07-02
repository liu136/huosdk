<?php 
	$response = {
        "environment" = "Sandbox";
        "receipt" =         {
            "adam_id" = 0;
            "app_item_id" = 0;
            "application_version" = 1;
            "bundle_id" = "DaMaiassistant";
            "download_id" = 0;
            "in_app" =             (
                                {
                    "is_trial_period" = false;
                    "original_purchase_date" = "2016-08-22 11:22:39 Etc/GMT";
                    "original_purchase_date_ms" = 1471864959000;
                    "original_purchase_date_pst" = "2016-08-22 04:22:39 America/Los_Angeles";
                    "original_transaction_id" = 1000000230967011;
                    "product_id" = "COL_SDK288";
                    "purchase_date" = "2016-08-22 11:22:39 Etc/GMT";
                    "purchase_date_ms" = 1471864959000;
                    "purchase_date_pst" = "2016-08-22 04:22:39 America/Los_Angeles";
                    "quantity" = 1;
                    "transaction_id" = 1000000230967011;
                }
            );
            "original_application_version" = "1.0";
            "original_purchase_date" = "2013-08-01 07:00:00 Etc/GMT";
            "original_purchase_date_ms" = 1375340400000;
            "original_purchase_date_pst" = "2013-08-01 00:00:00 America/Los_Angeles";
            "receipt_creation_date" = "2016-08-22 11:22:39 Etc/GMT";
            "receipt_creation_date_ms" = 1471864959000;
            "receipt_creation_date_pst" = "2016-08-22 04:22:39 America/Los_Angeles";
            "receipt_type" = "ProductionSandbox";
            "request_date" = "2016-08-22 11:22:46 Etc/GMT";
            "request_date_ms" = 1471864966367;
            "request_date_pst" = "2016-08-22 04:22:46 America/Los_Angeles";
            "version_external_identifier" = 0;
        };
        "status" = 0;
    };
		echo "aa";
		$receipt=$response->{'receipt'};
		 $in_app=$receipt->{'in_app'};
		 print_r($in_app);
		 $strlens=strlen($in_app);
		 $in_app=substr($in_app,1,$strlens-2);

?>