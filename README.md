Description
----
This plugin provides functionality for fetching and viewing objects in a table. It is meant to be use with the CommonGateway.

## How to use

- Upload plugin .zip on the Plugins page. 
- Activate plugin
- Check the Settings -> ObjectTable page.
- Add a configuration with API URL and API KEY.
- You can also choose to add a css class to the table if there is one.
- You can choose to add a optional mapping JSON for example {"name": "naam"}
- Add a shortcode to a page of choice like: [object-table configId="2"] where configId is the id of one of your configurations on the settings page.
- When visiting the page the shortcode will trigger code that fetches the data and renders a table. 

## Dependencies

You will need authorization credentials and endpoints from a CommonGateway instance or any other data source to fetch data from.