// create subscriptionId.
		$subscriptionId = 'SUBS' . date( 'ymdHis' );

		// create user subscription on PhonePe
		$res = DNM_Helper::create_subscription( $subscriptionId, $phone, $amount, 'MONTHLY', 12 );

		$state = $res['state'];

		$transactionID = 'TRANS' . date( 'ymdHis' );

		if ( $state == 'CREATED' ) {
			$response = DNM_Helper::setup_mandate_or_accept_payment( $merchantId, $merchantUserId, $subscriptionId, $transactionID, $saltKey, $saltIndex, $callbackUrl, 'ANDROID', 'UPI_COLLECT', 'com.phonepe.app' );
		}

		var_dump( $response );
		die;

		// Check the response
		// if ( $responseData['success'] === true ) {
		// echo 'Subscription status fetched successfully. Subscription ID: ' . $responseData['data']['subscriptionId'] . '. Status: ' . $responseData['data']['state'];
		// } else {
		// echo 'Failed to fetch subscription status. Error: ' . $responseData['message'];
		// }