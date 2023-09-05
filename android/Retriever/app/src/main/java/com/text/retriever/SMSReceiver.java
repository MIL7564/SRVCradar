package com.text.retriever;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class SMSReceiver extends BroadcastReceiver {

    private static final String TAG = "SMSReceiver";
    private static final MediaType JSON = MediaType.parse("application/json; charset=utf-8");
    private static final String WEBHOOK_URL = "https://your-server-address.com/my-webhooks.php";

    @Override
    public void onReceive(Context context, Intent intent) {
        Bundle data = intent.getExtras();

        Object[] pdus = (Object[]) data.get("pdus");
        if (pdus != null) {
            for (Object pdu : pdus) {
                SmsMessage smsMessage = SmsMessage.createFromPdu((byte[]) pdu);
                String sender = smsMessage.getDisplayOriginatingAddress();
                String messageBody = smsMessage.getMessageBody();

                Log.i(TAG, "Received SMS: " + messageBody + ", Sender: " + sender);

                if (messageBody.toLowerCase().contains("cellnet") && messageBody.toLowerCase().contains("opa")) {
                    String TICKET = getHumanReadableNanoTime();
                    Log.i(TAG, "Generated TICKET: " + TICKET);
                    new WebhookAsyncTask().execute(messageBody, sender.substring(1, 4), TICKET);
                }
            }
        }
    }

    private String getHumanReadableNanoTime() {
        long currentTimeNs = System.nanoTime();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss.SSSSSS", Locale.getDefault());
        Date resultDate = new Date(currentTimeNs / 1000000);  // Convert nanoseconds to milliseconds
        return sdf.format(resultDate) + " AFTER DISAPPEARANCE";
    }

    private class WebhookAsyncTask extends AsyncTask<String, Void, Void> {
        OkHttpClient client = new OkHttpClient();

        @Override
        protected Void doInBackground(String... params) {
            String messageBody = params[0];
            String fromNumber = params[1];
            String TICKET = params[2];

            try {
                String requestBody = "{\"text\":\"" + messageBody + "\",\"FromNumber\":\"" + fromNumber + "\",\"TICKET\":\"" + TICKET + "\"}";

                RequestBody body = RequestBody.create(requestBody, JSON);
                Request request = new Request.Builder()
                        .url(WEBHOOK_URL)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .addHeader("FromNumber", fromNumber)
                        .addHeader("text", messageBody)
                        .addHeader("TICKET", TICKET)
                        .build();

                client.newCall(request).execute();
            } catch (Exception e) {
                Log.e(TAG, "Error sending to webhook: ", e);
            }

            return null;
        }
    }
}
