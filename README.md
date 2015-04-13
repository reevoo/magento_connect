# Reevoo Magento Connect Project

## Module Project Status

Waiting for testing and quality assurance.

---

### Important Notes

1. **Requirements:** Magento Community 1.6.xx up-to 1.9.xx, Magento Enterprises (1.13.xx to 1.14.xx).
2. **Server Side Requirements: _MySQL 5.0 and above PHP 5.3.x and above._** Required Libraries are _ftp_connect_, _ftp_ssl_connect_, _ftp_get_, _ftp_fget_, _Net_SFTP_, _curl_.
3. **All functionality is tested on Magento Community and can be assured to work perfectly on editions 1.6.2 and above upto 1.9.1.0**
4. **Magento Enterprises is also supported but not tested.**
5. **For your safety related to any damage or loss to your store, its strongly recommended to install and test this on your development environment and take a backup copy as first.**

---

#### Installation

1. Disable your Magento Cache.
2. Disable Compilation by going to System->Tools->Compilation and hit Disable Compilation (if available) .
3. Copy all the files to Magento Root.
4. Logout and login to system (this is compulsory otherwise you will receive 404 errors in admin panel).
4. Navigate to System->Configuration->Reevoo and setup your configuration per instruction / manual from Reevoo.

---

#### Debugging/ Removal

Usually debugging requires technical understanding with Magento and a pro can help you in this regard. However as a basic troubleshoot for temporarily purposes of extension we suggest you to **disable** the extension.

**How To Disable:** Go to app/etc/modules and open file EComHut_Reevoo.xml for editing and change the value <active>**true**</active> to <active>**_false**</active> and save.

In any circumstances you unfortunately requires to delete the module in full, please follow the following instructions very carefully. (Technical understanding is a must for this step).

**How to Delete Reevoo:** To delete the Reevoo from your Magento, you need to delete extension files and a database table. See files and directory list in full below.

```
app/etc/modules/EComHut_Reevoo.xml  _(Delete this file)_
app/code/local/EComHut/Reevoo  _(Delete this folder and folder contents)_
app/design/frontend/base/default/layout/reevoo.xml  _(Delete this file)_
app/design/frontend/base/default/template/reevoo _(Delete this folder and folder contents)_
lib/Reevoo _(Delete this folder and folder contents)_
```

---

For any questions and or inquiry, please contact with [http://innovadeltech.com](http://innovadeltech.com "Innovadel Technologies Limited")