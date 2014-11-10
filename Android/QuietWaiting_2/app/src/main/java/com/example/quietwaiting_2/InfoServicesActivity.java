package com.example.quietwaiting_2;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.TextView;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class InfoServicesActivity extends Activity {
    private JSONParser jsonParser;
    private int id;
    private int id_ticket;
    private static final String ADRESSE_IP_JSON = "http://10.16.162.194/api/";
    private TextView show_number;

    @Override
        protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_info_services);
        show_number = (TextView)findViewById(R.id.show_ticket);
        show_number.setText(null);
        Intent i = this.getIntent();
        String service = i.getStringExtra("name_service");
        id = i.getIntExtra("id_service", 0);
        Log.d("ID_SERVICES",String.valueOf(id));
        new Thread(new Runnable() {
            @Override
            public void run() {
                getTicket(id);
            }
        }).start();


        setTitle(service);

    }

    public void getTicket(int id)
    {
        setProgressBarIndeterminateVisibility(true);
        jsonParser = new JSONParser();
        //Create service parameter
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        //params.add(new BasicNameValuePair("id_service", String.valueOf(id)));

        String url_link = ADRESSE_IP_JSON + "bornes/request_ticket?id=1&id_service="+id;
        Log.d("URL_LINK",url_link);
        String method = "GET";
        JSONObject json = jsonParser.getJSONFromUrlInObject(url_link, method, params);
        try {
            id_ticket = Integer.valueOf(json.getString("id"));
        } catch (JSONException e) {
            Log.d("Error",e.getMessage());
        }
        runOnUiThread(new Runnable() {
            @Override
            public void run() {
                show_number.setText("Vous avez le ticket numéro : "+String.valueOf(id_ticket));
            }
        });
        setProgressBarIndeterminateVisibility(false);
    }
}
