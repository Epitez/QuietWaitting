package com.example.quietwaiting_2;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import org.apache.http.NameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class ListActivity extends Activity {
	String[] services;
    int[] idServices;
	 Intent intent_for_info_services;
    private JSONParser jsonParser;
    private static final String ADRESSE_IP_JSON = "http://10.16.162.194/api/";
    private ListView listView ;
    private ArrayList<String> list = new ArrayList<String>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
        setContentView(R.layout.activity_list);
        listView = (ListView) findViewById(R.id.list_services);
        new Thread(new Runnable() {
            @Override
            public void run() {
                services = getServices();
            }
        }).start();

    }
    
    private class StableArrayAdapter extends ArrayAdapter<String> {

        HashMap<String, Integer> mIdMap = new HashMap<String, Integer>();

        public StableArrayAdapter(Context context, int textViewResourceId,
            List<String> objects) {
          super(context, textViewResourceId, objects);
          for (int i = 0; i < objects.size(); ++i) {
            mIdMap.put(objects.get(i), i);
          }
        }

        @Override
        public long getItemId(int position) {
          String item = getItem(position);
          return mIdMap.get(item);
        }

        @Override
        public boolean hasStableIds() {
          return true;
        }

    }

    protected String[] getServices() {
        setProgressBarIndeterminateVisibility(true);
        jsonParser = new JSONParser();

        String[] allServices = null;
        int[] allIdServices = null;
        List<NameValuePair> params = new ArrayList<NameValuePair>();
        //params.add(new BasicNameValuePair("tag", "1"));
        String url_link = ADRESSE_IP_JSON+"services/";
        //String url_link = "http://graph.facebook.com/bgolub";
        String method = "GET";
        JSONArray json = jsonParser.getJSONFromUrl(url_link, method, params);
        if(json != null) {


            allServices = new String[json.length()];
            allIdServices = new int[json.length()];

            //Try to get Json Result
            try {
                if (json.length() > 0) {

                    for (int i = 0; i < json.length(); i++) {
                        JSONObject service = json.getJSONObject(i);
                        allIdServices[i] = Integer.valueOf(service.getString("id"));
                        allServices[i] = String.valueOf(service.getString("name"));
                        Log.d("Nom :", String.valueOf(service.getString("name")));
                    }
                    services = allServices;
                    idServices = allIdServices;
                    runOnUiThread(new Runnable() {
                        @Override
                        public void run() {


                            if (services == null) {
                                services[0] = "Aucun service";
                            } else {
                                for (int i = 0; i < services.length; ++i) {
                                    list.add(services[i].toString());

                                }
                            }
                            ArrayAdapter<String> adapter = new ArrayAdapter<String>(getApplicationContext(),
                                    android.R.layout.simple_list_item_1, list) {
                                @Override
                                public View getView(int position, View convertView, ViewGroup parent) {
                                    View view = super.getView(position, convertView, parent);
                                    TextView text = (TextView) view.findViewById(android.R.id.text1);
                                    text.setTextColor(Color.BLACK);
                                    return view;
                                    }
                                };
                            listView.setAdapter(adapter);

                            intent_for_info_services = new Intent(getApplicationContext(), InfoServicesActivity.class);
                            listView.setOnItemClickListener(new OnItemClickListener() {

                                @Override
                                public void onItemClick(AdapterView<?> parent, View view,
                                                        int position, long id) {

                                    intent_for_info_services.putExtra("name_service", services[(int) id]);
                                    intent_for_info_services.putExtra("id_service", idServices[(int)id]);
                                    startActivity(intent_for_info_services);

                                }
                            });
                            setProgressBarIndeterminateVisibility(false);
                        }
                    });
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }else
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    setProgressBarIndeterminateVisibility(false);
                    Toast.makeText(getApplicationContext(),
                            "Aucun service trouvé !", Toast.LENGTH_LONG).show();
                }
            });
        return allServices;
    }
}
