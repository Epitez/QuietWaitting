package com.example.quietwaiting_2;

import android.util.Log;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.List;

public class JSONParser {

    static InputStream is = null;
    static JSONArray json = null;
    static String outPut = "";
    static JSONObject jsonObj;

    // constructor
    public JSONParser() {

    }

    public JSONArray getJSONFromUrl(String url, String method, List<NameValuePair> params) {

        // Making the HTTP request
        try {

            DefaultHttpClient httpClient = new DefaultHttpClient();
            HttpResponse httpResponse;
            if(method == "GET" || method == "get")
            {
                HttpGet request = new HttpGet(url);
                httpResponse = httpClient.execute(request);
            }
            else
            {
                HttpPost request = new HttpPost(url);
                request.setEntity(new UrlEncodedFormEntity(params));
                httpResponse = httpClient.execute(request);
            }

            HttpEntity httpEntity = httpResponse.getEntity();
            is = httpEntity.getContent();

            BufferedReader in = new BufferedReader(new InputStreamReader(
                    is, "iso-8859-1"), 8);
            StringBuilder sb = new StringBuilder();
            String line = null;
            while ((line = in.readLine()) != null) {
                sb.append(line + "\n");
            }
            is.close();
            outPut = sb.toString();
            Log.e("JSON", outPut);

            json = new JSONArray(outPut);

        } catch (UnsupportedEncodingException e) {
            Log.d("error",e.getMessage());
        } catch (ClientProtocolException e) {
            Log.d("error", e.getMessage());
        } catch (IOException e) {
            Log.d("error",e.getMessage());
            json = null;
        } catch (JSONException e) {
            Log.e("JSON Parser", "Error parsing data " + e.toString());
        }

        return json;

    }

    public JSONObject getJSONFromUrlInObject(String url, String method, List<NameValuePair> params) {

        // Making the HTTP request
        try {

            DefaultHttpClient httpClient = new DefaultHttpClient();
            HttpResponse httpResponse;
            if(method == "GET" || method == "get")
            {
                HttpGet request = new HttpGet(url);
                httpResponse = httpClient.execute(request);
            }
            else
            {
                HttpPost request = new HttpPost(url);
                request.setEntity(new UrlEncodedFormEntity(params));
                httpResponse = httpClient.execute(request);
            }

            HttpEntity httpEntity = httpResponse.getEntity();
            is = httpEntity.getContent();

            BufferedReader in = new BufferedReader(new InputStreamReader(
                    is, "iso-8859-1"), 8);
            StringBuilder sb = new StringBuilder();
            String line = null;
            while ((line = in.readLine()) != null) {
                sb.append(line + "\n");
            }
            is.close();
            outPut = sb.toString();
            Log.e("JSON", outPut);

            jsonObj = new JSONObject(outPut);

        } catch (UnsupportedEncodingException e) {
            Log.d("error",e.getMessage());
        } catch (ClientProtocolException e) {
            Log.d("error", e.getMessage());
        } catch (IOException e) {
            Log.d("error",e.getMessage());
            json = null;
        } catch (JSONException e) {
            Log.e("JSON Parser", "Error parsing data " + e.toString());
        }

        return jsonObj;

    }
}
