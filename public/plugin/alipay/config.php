<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016093000629542",

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEArK7aKcM9nZTkTYWEiRYD0VjpUWaxPsTb1h4B7SHO3pcJykPeZvLyHI/dOfvDbet/DVfCL9YF+4sP+B4zStxCesK1deKC4ZvzksyvNs0JwFIKv/vxQv3HGJZqh7xZyW+aTIus00M2rwTRzyrmwbZSXW4wxrr9xsWivoUBCvyNlkHdSTM3EpfHo7/2yg2VXlIa7slR8RIjG/y9lM71R8TrrteNp1Ew4nseNKV+UsB26LSsXzsg+XsapI9kTwwWvhf5Bq3J1DV7X37kvWuE1CH0v8d6Wo/urjkpoCjg8avUfxgHwLjfSR5aum+ECI3W1QXtl700jyLV4RQj7j2FiyVTywIDAQABAoIBAEG2LAjWZh0QKqyhUWUFPqCsj+TNCkfTi8B4HM2LHvivSydwGjLc4msiB9Jnzt+GTQvbyGaBsDcfnQ0TGPu0w4uJipn4f0uxF8hWIZrd/Yb08WhLTGzsu5XMr29FqnkwAex8/mBKZIXHdLu8HKG8aq1pICCPycCZNqv/Wv0+S/bQKweORYhShaOeE0Olg250BAY42niFZJfydNAP7kzAupl78sEl3/3X1cQkdcg4WUkZeNaLLfoVsf5ejF5zFRFV06vmiEIxTD8/MgCmiHKsMo1rHKqXyUpqyKhyg3Vt5yavhFL4N5FchxIjL+dx5Xj4RdeYTZN2M6rdy2x1624I3GECgYEA48ih3q9rR2aN3KVnoBESHlCQH9bLE240KhoYiMLSydP9cwZr+fx+zSeIVhixphm7ndD2dZ37LbNU6/27sOzSqCnpsIu6e+nGV+6g6PJ++8eqqW804eLbIfHhCFvTZlb+37I6uHqaVMC1nfvFaeYpOWqOse6uO9FWfk3QoCQbN9sCgYEAwhLklWgxYJHn4xWU2xhug2qM6QrfA3hfuzsFRroGreNyze1VLINXHW20pkOqJCKydFqihxFZLzkZQ+eB8Rl/KMpZMmovZs2Axwhfc3ne8uqwd7gY/t93KxfDM7qxd+7lAosLFc1FzW3FFkyncdyVKyuWz8gCTJcprwGcfwo4TtECgYB+/y5YlJ8QWPpBCatD0CC07B/e0Ie6lpfY3WHOZijVxC6rwenTn9YmlaLqtaveOryi2Y1/uAwBx9lSFc5ebztn7K5Q8yaOzaD7B52JIyJvSDw7aTgntK6ON2hKm7+6cEh7+cfJcPm4xRS9N1EyoNNdIq7aP4O+8BD0LHhjwJ24PwKBgEeAEGkfHvn2J1mZbOMfaC/QhKOjFrC/m31ubC43gRh1cKl/o96ncOPttB2BVmDDy71kWvaqJJJqVe2XkF8URq3vhtc1GculMKmjYw83OjWC04r373WPPOdKdOdNA+8su4CpW2cn3myl4XOqwBIJ84cZIxHgHUC8fGX2kQKY8E/RAoGBAKYMQirhuxNhuATXRkDl+A4zvV9OnNdVoksScLVLnYkalLGwfZgfvN/lOU5xDJ5hbZaAPpzQ4Kr66rHTpPRlFp2uDFLlefmFaZhgIY8DBxJw6oo1haaMvpLfw3wFGz/wBjIUJQg3oo8WIp3sRvt/MIyMjHlPRnG/E+oj0Rq5Wisz",
		
		//异步通知地址
		'notify_url' => "http://www.pyg.com/home/OrderController/notify",
		
		//同步跳转
		'return_url' => "http://www.pyg.com/home/Order_controller/callback",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6dnSBz35mJsZQlem25saOQUqLSnHJtr3Aed/NeZ3FDmeTk848BE57Xq1y4arPeLgB34yfKUn2bvZ5LPAzAAAmHSF6fB7rURcYXhmNfA4UDao5H1m5c+JTFQj81+pcLEYZCLSWL8ZRhce4S9kBvitaUl0W8dAdDa7f7iWog7dpzeLJ2wg8O8HtRbeUwaZqqz/8wDD4qKw9626e5ZZ2tiPDeKC0uf9Tw+zAsffFsViJmy7CtqQslUDOT1mHQcvNKG+2z27g72PR1IyEgNUuGG88hp/m/tITuXUT/CCR2ENM1/BkPafaNDlkFWa94Bt1oTNJXq714mKzhhKTH/G17facwIDAQAB",
);