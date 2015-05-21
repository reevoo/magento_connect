# Reevoo Magento Connect Project

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

##License

This software is released under the MIT license.  Only certified Reevoo partners
are licensed to display Reevoo content on their sites.  Contact <sales@reevoo.com> for
more information.

(The MIT License)

Copyright (c) 2008 - 2014:

* [Reevoo](http://www.reevoo.com)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
