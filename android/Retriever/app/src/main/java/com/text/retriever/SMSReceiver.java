package com.text.retriever;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;  // <-- import the Log class

public class SMSReceiver extends BroadcastReceiver {
    private static final String TAG = "SMSReceiver";  // <-- define a TAG for your logs

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

                    // Convert the message body and keyword to lowercase for case-insensitive comparison
                    String lowerCaseMessageBody = messageBody.toLowerCase();
                    String lowerCaseKeyword = "sentinel".toLowerCase();

                    if (lowerCaseMessageBody.contains(lowerCaseKeyword)) {
                        // Keyword "sentinel" (case-insensitive) found in the message
                        // Log the fact that the keyword "sentinel" was found in the SMS
                        Log.i(TAG, "SMS contained the word 'sentinel'");  // <-- log message
                    }
                }
            }
        }
    }
}
