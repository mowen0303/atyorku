/*
 * PubMedServer
 * 1.0
 * Copyright (c) 2017-2018 Jeremiah Guevarra, Veena Heer, Rochelle Etwaroo, Yumin Sun, Peter Lou. All Rights Reserved. 
 */
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.params.ClientPNames;
import org.apache.http.client.params.CookiePolicy;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

/**
 * This class is responsible for connecting to the PubMed server to get data back from it
 * @version  1.0 October 6, 2017
 * @author Jeremiah Guevarra, Veena Heer, Rochelle Etwaroo, Yumin Sun, Peter Lou
 */
public class PubMedServer 
{

    private String host;
    
    /**
     * A default constructor
     */
    public PubMedServer() 
    {
        host = "http://www.ncbi.nlm.nih.gov/";
    }

    /**
     * A method that uses the PubMed API to search for the article
     * @param entryNumber articleList . used to match the article and pmid
     * @return the response from the server in a form of a string
     */
    
    
    //couple modifications on eSearchArticle method
    public String eSearchArticle(String entryNumber, String[] articleList) 
    {
        try 
        {
            //The URL when using E-Utilities
            String search = "entrez/eutils/esearch.fcgi?db=pubmed&term=";
            search += articleList[(Integer.parseInt(entryNumber.replaceFirst("^0*", ""))) - 1].replace(" ", "+");
            search=search.replace("\"", "");
            search=search.replace("{", "");
            search=search.replace("}", "");
            search=search.replace("%", "");
            
           
            search += "&field=title";
            
            HttpGet hGet = new HttpGet(host + search);
            HttpClient hClient = new DefaultHttpClient();
            
            //To remove the cookie warning
            hClient.getParams().setParameter(ClientPNames.COOKIE_POLICY, CookiePolicy.BROWSER_COMPATIBILITY);
            
            //Get the content of the response
            HttpResponse hResponse = hClient.execute(hGet);
            HttpEntity hEntity = hResponse.getEntity();

            //Return the content in a string
            return EntityUtils.toString(hEntity);
            
        } 
        catch (Exception e) 
        {
            System.out.println("\n\n" + e.getMessage() + "\n\n");
            return "";
        }
    }
    
    // same method as eSearchArticle, just modify the input argument 
    public String eSearchArticleInt(int i, String[] articleList) 
    {
        try 
        {
            //The URL when using E-Utilities
            String search = "entrez/eutils/esearch.fcgi?db=pubmed&term=";
            search += articleList[i].replace(" ", "+");
            search=search.replace("\"", "");
            search=search.replace("{", "");
            search=search.replace("}", "");
            search=search.replace("%", "");
            
           
            search += "&field=title";
            
            HttpGet hGet = new HttpGet(host + search);
            HttpClient hClient = new DefaultHttpClient();
            
            //To remove the cookie warning
            hClient.getParams().setParameter(ClientPNames.COOKIE_POLICY, CookiePolicy.BROWSER_COMPATIBILITY);
            
            //Get the content of the response
            HttpResponse hResponse = hClient.execute(hGet);
            HttpEntity hEntity = hResponse.getEntity();

            //Return the content in a string
            return EntityUtils.toString(hEntity);
            
        } 
        catch (Exception e) 
        {
            System.out.println("\n\n" + e.getMessage() + "\n\n");
            return "";
        }
    }
    
    public void setHost(String host)
    {
        this.host = host;
    }
    
    public String getHost()
    {
        return host;
    }
}
