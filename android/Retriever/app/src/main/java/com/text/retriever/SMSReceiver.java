package com.text.retriever;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

import java.util.concurrent.TimeUnit;

public class SMSReceiver extends BroadcastReceiver {
    private static final String TAG = "SMSReceiver";
    private OkHttpClient client;
    private static boolean isProcessingSMS = false;  // Added variable
    private static final String WEBHOOK_URL = "https://FlowerEconomics.com/wp-json/my-webhooks/v1/webhook/text";
    private static final MediaType JSON = MediaType.get("application/json; charset=utf-8");

    public SMSReceiver() {
        this.client = new OkHttpClient.Builder()
                .connectTimeout(20, TimeUnit.SECONDS)
                .readTimeout(20, TimeUnit.SECONDS)
                .build();
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if ("android.provider.Telephony.SMS_RECEIVED".equals(intent.getAction())) {
            // Check if the SMS is already being processed
            if (isProcessingSMS) {
                // If the SMS is being processed, ignore this broadcast
                return;
            }

            // Mark the SMS as being processed
            isProcessingSMS = true;

            Bundle extras = intent.getExtras();
            if (extras != null) {
                Object[] pdus = (Object[]) extras.get("pdus");
                if (pdus != null && pdus.length > 0) {
                    SmsMessage[] messages = new SmsMessage[pdus.length];
                    for (int i = 0; i < pdus.length; i++) {
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                            // ... Rest of the message processing code ...

                            // IMPORTANT: Remove the abortBroadcast() call
                        }
                    }
                }
            }

            // Mark the SMS processing as complete
            isProcessingSMS = false;
        }
    }

    private class WebhookAsyncTask extends AsyncTask<String, Void, Void> {
        @Override
        protected Void doInBackground(String... params) {
            String messageBody = params[0];
            String fromNumber = params[1];

            try {
                String requestBody = "{\"text\":\"" + messageBody + "\",\"FromNumber\":\"" + fromNumber + "\"}";
                RequestBody body = RequestBody.create(requestBody, JSON);
                Request request = new Request.Builder()
                        .url(WEBHOOK_URL)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .addHeader("FromNumber", fromNumber)
                        .addHeader("text", messageBody)
                        .build();

                // Execute the request
                Response response = client.newCall(request).execute();

                Log.i(TAG, "Webhook response: " + response.body().string());
            } catch (Exception e) {
                Log.e(TAG, "Error in sending request", e);
            }

            return null;
        }
    }

}
