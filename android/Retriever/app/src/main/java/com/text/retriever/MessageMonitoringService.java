package com.text.retriever;

import android.app.Service;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.IBinder;

public class MessageMonitoringService extends Service {

    private static boolean isServiceRunning = false;  //add this static flag
    private SMSReceiver yourBroadcastReceiver;

    @Override
    public void onCreate() {
        super.onCreate();
        // Set the flag to true when the service starts
        isServiceRunning = true;
        // Perform initialization tasks here
        yourBroadcastReceiver = new SMSReceiver();
        IntentFilter intentFilter = new IntentFilter("android.provider.Telephony.SMS_RECEIVED");
        registerReceiver(yourBroadcastReceiver, intentFilter);
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        // Perform tasks when the service starts here
        return Service.START_STICKY;
    }

    @Override
    public IBinder onBind(Intent intent) {
        // Return null for a simple Service
        return null;
    }

    @Override
    public void onDestroy() {
        // Perform cleanup tasks here
        super.onDestroy();

        // Set the flag to false when the service stops
        isServiceRunning = false;
        unregisterReceiver(yourBroadcastReceiver);
    }

    // Add a static method to check the service stats
    public static boolean isServiceRunning() {
        return isServiceRunning;
    }
}
