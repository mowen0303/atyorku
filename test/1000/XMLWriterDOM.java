import java.io.File;
import java.text.DecimalFormat;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;





public class XMLWriterDOM {
	
	
   
    public XMLWriterDOM() {
    	 
     
    }

	
	
	public static void main(String[] args) {
		DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder dBuilder;
		
		DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
		DocumentBuilder builder;
		Document document;
	
		
		 String[] list = new String[0];
		
		
		 try 
		 {
			 factory = DocumentBuilderFactory.newInstance();
			 builder = factory.newDocumentBuilder();
	            File xFile = new File("C:/Users/Sun/workspace/4020a1/4020a1-datasets.xml");
	            document = builder.parse(xFile);
	            document.getDocumentElement().normalize();
	            NodeList nList = document.getElementsByTagName("PubmedArticle");
	            if (nList.getLength() > 0) 
	            {
	                list = new String[nList.getLength()];
	                for (int i = 0; i < nList.getLength(); i++) 
	                {
	                	Node node = nList.item(i);
	                    if (node.getNodeType() == Node.ELEMENT_NODE) 
	                    {
	                         Element element = (Element) node;
	                        list[i] = element.getElementsByTagName("ArticleTitle").item(0).getTextContent();
	                    }
	                }
	            }
	            
	            
	        } 
	        catch (Exception e) 
	        {
	            System.out.println("\n\n" + e.getMessage() + "\n\n");
	            
	        }
		
		
		
		
		
		
		
		
		
		
		
		try {
			dBuilder = dbFactory.newDocumentBuilder();
			Document doc = dBuilder.newDocument();
			
			PubMedServer pmServer = new PubMedServer();
			GroupServer server= new GroupServer();
			XmlManager xManager= new XmlManager();
			//add elements to Document 
			Element rootElement =
					doc.createElementNS("", "PubmedArticleSet");
			//append root element to document
			doc.appendChild(rootElement);
			
			
			// for loop used to generate the final XML file , now working on how to add thread here 
			 for(int i=0;i<list.length;i++){
				 String[] pmid = xManager.readXmlResponse(pmServer.eSearchArticleInt(i, list));
				 if (pmid[0]==null){
					 rootElement.appendChild(getChild(doc, "aError"+Integer.toString(i),list[i]));
					 }
				 else{
			rootElement.appendChild(getChild(doc, pmid[0],list[i]));
			}
			 }
			  
			



            //for output to file&console

            TransformerFactory transformerFactory = TransformerFactory.newInstance();

            Transformer transformer = transformerFactory.newTransformer();

            //for pretty print

            transformer.setOutputProperty(OutputKeys.INDENT, "yes");

            DOMSource source = new DOMSource(doc);



            //write to console&file

            StreamResult console = new StreamResult(System.out);

            StreamResult file = new StreamResult(new File("C:/Users/Sun/workspace/4020a1/finalResult.xml"));
         



            //write data

            transformer.transform(source, console);

            transformer.transform(source, file);

            System.out.println("DONE");



        } 
        catch (Exception e) {
        	e.printStackTrace();
        	}
        }
    //foramt the child node
    private static Node getChild(Document doc, String id, String title) {

        Element child = doc.createElement("PubmedArticle");
        child.appendChild(getChildElements(doc, child, "PMID", id));
        child.appendChild(getChildElements(doc, child, "ArticleTitle", title));
        return child;

    }
    
    private static Node getChildElements(Document doc, Element element, String name, String value) {
    	Element node = doc.createElement(name);
    	node.appendChild(doc.createTextNode(value));
    	return node;
    	}
   
   
    }