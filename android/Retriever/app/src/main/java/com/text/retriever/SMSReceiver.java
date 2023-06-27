package com.text.retriever;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.SmsMessage;

public class SMSReceiver extends BroadcastReceiver {
    @Override
    public void onReceive(Context context, Intent intent) {
        Bundle extras = intent.getExtras();
        if (extras != null) {
            Object[] pdus = (Object[]) extras.get("pdus");
            if (pdus != null && pdus.length > 0) {
                SmsMessage[] messages = new SmsMessage[pdus.length];
                for (int i = 0; i < pdus.length; i++) {
                    messages[i] = SmsMessage.createFromPdu((byte[]) pdus[i]);
                }

                // Extract necessary information from the messages
                String messageBody = messages[0].getMessageBody();
                String sender = messages[0].getOriginatingAddress();
                // Extract other relevant information as needed

                // Convert the message body and keyword to lowercase for case-insensitive comparison
                String lowerCaseMessageBody = messageBody.toLowerCase();
                String lowerCaseKeyword = "sentinel".toLowerCase();

                if (lowerCaseMessageBody.contains(lowerCaseKeyword)) {
                    // Keyword "sentinel" (case-insensitive) found in the message
                    // Perform desired action here
                    // ...
                }
            }
        }
    }
}
