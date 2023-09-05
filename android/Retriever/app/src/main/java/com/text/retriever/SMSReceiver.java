package com.text.retriever;

import java.io.IOException;
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

                    // Extract necessary information from the messages
                    String messageBody = messages[0].getMessageBody();
                    String sender = messages[0].getOriginatingAddress();

                    // Convert the message body and keywords to lowercase for case-insensitive comparison
                    String lowerCaseMessageBody = messageBody.toLowerCase();
                    String keyword1 = "cellnet";
                    String keyword2 = "opa";

                    if (lowerCaseMessageBody.contains(keyword1) && lowerCaseMessageBody.contains(keyword2)) {
                        // Keywords "cellnet" and "opa" (case-insensitive) found in the message
                        // Log the entire message and sender's number (second, third, and fourth digits)
                        Log.i(TAG, "SMS contained the word 'cellnet' and 'opa': " + messageBody);
                        if (sender != null && sender.length() > 4) {
                            Log.i(TAG, "Sender's number, digits Two TO Four: " + sender.substring(1, 4));
                            // Trigger the webhook asynchronously using AsyncTask
                            // new WebhookAsyncTask().execute(escapeJsonString(messageBody), sender.substring(1, 4));
                            WebhookAsyncTask escapeJsonString = new WebhookAsyncTask();
                            escapeJsonString.execute(messageBody, sender.substring(1,4));
                        } else {
                            Log.w(TAG, "Sender's number is not long enough to extract digits Two TO Four");
                        }
                    }
                }
            }
        }
    }


    public class RunPythonFromJava {
        public String getPythonOutput() {
            String pythonScriptPath = "../../../../../python/dispenser.py"; // Replace with the actual path
            String output = "";

            try {
                // Create a ProcessBuilder to run the Python script
                ProcessBuilder processBuilder = new ProcessBuilder("python", pythonScriptPath);
                processBuilder.redirectErrorStream(true);
                Process process = processBuilder.start();

                java.io.InputStream inputStream = process.getInputStream();
                java.util.Scanner scanner = new java.util.Scanner(inputStream).useDelimiter("\\A");
                output = scanner.hasNext() ? scanner.next() : "";

                int exitCode = process.waitFor();
                Log.i(TAG, "Python process exited with code: " + exitCode);
            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
            }
            Log.i(TAG, "Ran Python Dispenser, output:" + output);
            return output;
        }
    }


    private class WebhookAsyncTask extends AsyncTask<String, Void, Void> {
        @Override
        protected Void doInBackground(String... params) {
            String messageBody = params[0];
            String fromNumber = params[1];

            // Get Python script output
            RunPythonFromJava pythonRunner = new RunPythonFromJava();
            String TICKET = pythonRunner.getPythonOutput().trim();

            try {
                String requestBody = "{\"text\":\"" + messageBody + "\",\"FromNumber\":\"" + fromNumber + "\"}";
                RequestBody body = RequestBody.create(requestBody, JSON);
                Request request = new Request.Builder()
                        .url(WEBHOOK_URL)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .addHeader("FromNumber", fromNumber)
                        .addHeader("text", messageBody)
                        .addHeader("TICKET", TICKET) // Add TICKET as a header
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