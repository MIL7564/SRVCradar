package com.text.retriever;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;

public class SMSReceiver extends BroadcastReceiver {
    private static final String TAG = "SMSReceiver";

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

                    // Convert the message body and keyword to lowercase for case-insensitive comparison
                    String lowerCaseMessageBody = messageBody.toLowerCase();
                    String lowerCaseKeyword = "sentinel".toLowerCase();

                    if (lowerCaseMessageBody.contains(lowerCaseKeyword)) {
                        // Keyword "sentinel" (case-insensitive) found in the message
                        // Log the entire message and sender's number (second, third, and fourth digits)
                        Log.i(TAG, "SMS contained the word 'sentinel': " + messageBody);
                        if (sender != null && sender.length() > 4) {
                            Log.i(TAG, "Sender's number, digits 2-4: " + sender.substring(1, 4));
                        } else {
                            Log.w(TAG, "Sender's number is not long enough to extract digits 2-4");
                        }
                    }
                }
            }
        }
    }
}
