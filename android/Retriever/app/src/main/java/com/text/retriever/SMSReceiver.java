package com.text.retriever;

import java.time.LocalDate;
import java.time.LocalTime;
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

public class SMSReceiver extends BroadcastReceiver {
    private static final String TAG = "SMSReceiver";
    private OkHttpClient client;
    private static final String WEBHOOK_URL = "https://FlowerEconomics.com/wp-json/my-webhooks/v1/webhook/text";  // replace this with your actual URL
    private static final MediaType JSON = MediaType.get("application/json; charset=utf-8");

    // Define TheCurrentDate as a class member variable
    private LocalDate TheCurrentDate;
    private LocalTime TheCurrentTime;
    private String Taser;

    public SMSReceiver() {
        this.client = new OkHttpClient.Builder()
                .connectTimeout(20, java.util.concurrent.TimeUnit.SECONDS)
                .readTimeout(20, java.util.concurrent.TimeUnit.SECONDS)
                .build();
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if ("android.provider.Telephony.SMS_RECEIVED".equals(intent.getAction())) {
            Bundle extras = intent.getExtras();
            if (extras != null) {
                Object[] pdus = (Object[]) extras.get("pdus");
                if (pdus != null && pdus.length > 0) {
                    SmsMessage[] messages = new SmsMessage[pdus.length];
                    for (int i = 0; i < pdus.length; i++) {
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                            String format = extras.getString("format");
                            messages[i] = SmsMessage.createFromPdu((byte[]) pdus[i], format);
                        } else {
                            messages[i] = SmsMessage.createFromPdu((byte[]) pdus[i]);
                        }
                    }

                    // Extract necessary information from the messages
                    String messageBody = messages[0].getMessageBody();
                    String sender = messages[0].getOriginatingAddress();

                    // Convert the message body and keywords to lowercase for case-insensitive comparison
                    String lowerCaseMessageBody = messageBody.toLowerCase();
                    String keyword1 = "cellnet";
                    String keyword2 = "opa";

                    if (lowerCaseMessageBody.contains(keyword1) && lowerCaseMessageBody.contains(keyword2)) {
                        Log.i(TAG, "SMS contained the word 'cellnet' and 'opa': " + messageBody);
                        if (sender != null && sender.length() > 4) {
                            Log.i(TAG, "Sender's number, digits Two TO Four: " + sender.substring(1, 4));
                            // Update the time-related values
                            TheCurrentDate = LocalDate.now(java.time.ZoneId.of("America/New_York"));
                            TheCurrentTime = LocalTime.now(java.time.ZoneId.of("America/New_York"));
                            Taser = "Ticket#: " + TheCurrentDate + " " + TheCurrentTime;
                            WebhookAsyncTask escapeJsonString = new WebhookAsyncTask();
                            escapeJsonString.execute(messageBody, sender.substring(1, 4));
                        } else {
                            Log.w(TAG, "Sender's number is not long enough to extract digits Two TO Four");
                        }
                    }
                }
            }
        }
    }

    private class WebhookAsyncTask extends AsyncTask<String, Void, Void> {
        @Override
        protected Void doInBackground(String... params) {
            String messageBody = params[0];
            String fromNumber = params[1];

            try {
                String requestBody = "{\"text\":\"" + messageBody + "\",\"FromNumber\":\"" + fromNumber + "\",\"DatePersonal\":\"" + Taser + "\"}";
                RequestBody body = RequestBody.create(requestBody, JSON);
                Request request = new Request.Builder()
                        .url(WEBHOOK_URL)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .addHeader("FromNumber", fromNumber)
                        .addHeader("text", messageBody)
                        .addHeader("DatePersonal", Taser) // Add Taser as a header
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
