package com.text.retriever;

import java.util.Random;
import java.util.regex.Pattern;
import java.util.regex.Matcher;
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
    private static final String WEBHOOK_URL = "https://FlowerEconomics.com/wp-json/my-webhooks/v1/webhook/text";  // replace this with your actual URL
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

                    String messageBody = messages[0].getMessageBody();
                    String sender = messages[0].getOriginatingAddress();

                    Pattern pattern = Pattern.compile("\\b\\d{5}\\b");
                    Matcher matcher = pattern.matcher(messageBody);

// Check if the message contains "opa" and "cellnet"
                    if (messageBody.toLowerCase().contains("cellnet") && messageBody.toLowerCase().contains("opa")) {
                        Random random = new Random();
                        String fiveDigitNumber = String.format("%05d", random.nextInt(100000)); // random Ticket

                        if (matcher.find()) {
                            fiveDigitNumber = matcher.group(); // override with the actual 5-digit number if found
                        }

                        // Sending a response message with the parsed five-digit number
                        if (!fiveDigitNumber.isEmpty()) {
                            String responseMessage = "Your ticket number is: " + fiveDigitNumber;
                            SMSTransmitter.sendSMS(sender, responseMessage);
                        }

                        if (sender != null && sender.length() > 4) {
                            new WebhookAsyncTask().execute(escapeJsonString(fiveDigitNumber),
                                    escapeJsonString(messageBody),
                                    sender.substring(1, 4));
                        } else {
                            Log.w(TAG, "Sender's number is not long enough to extract digits Two TO Four");
                        }
                    } else {
                        Log.i(TAG, "Required keywords not found in the SMS");
                    }
                }
            }
        }
    }

    private String escapeJsonString(String input) {
        return input.replace("\\", "\\\\")
                .replace("\"", "\\\"")
                .replace("\n", "\\n")
                .replace("\r", "\\r")
                .replace("\t", "\\t");
    }

    private class WebhookAsyncTask extends AsyncTask<String, Void, Void> {
        @Override
        protected Void doInBackground(String... params) {
            String fiveDigitNumber = params[0];
            String messageBody = params[1];
            String fromNumber = params[2];

            try {
                String requestBody = "{\"fiveDigitNumber\":\"" + fiveDigitNumber + "\",\"text\":\"" + messageBody + "\",\"FromNumber\":\"" + fromNumber + "\"}";
                RequestBody body = RequestBody.create(requestBody, JSON);
                Request request = new Request.Builder()
                        .url(WEBHOOK_URL)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .addHeader("FromNumber", fromNumber)
                        .addHeader("text", messageBody)
                        .addHeader("fiveDigitNumber", fiveDigitNumber)
                        .build();

                Response response = client.newCall(request).execute();
                Log.i(TAG, "Webhook response: " + response.body().string());
            } catch (Exception e) {
                Log.e(TAG, "Error in sending request", e);
            }
            return null;
        }
    }
}
