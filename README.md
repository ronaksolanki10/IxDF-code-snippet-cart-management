
# Cart Management
I developed the Cart Management module for an existing eCommerce product, which allows cart operations to be accessed seamlessly across the portal for both logged-in and logged-out users. This module uses a ```Helper``` class to ensure consistent and efficient cart management.


## Files usage

### 1. AuthController.php

- The ```AuthController``` is responsible for handling user authentication. It includes logic to transfer the logged-out user's cart into the logged-in user's cart when the user logs in or registers. This ensures that any items the user added to their cart while logged out are preserved, except for items already present in the logged-in user's cart.

### 2. CartController.php
- The ```CartController``` serves as the API endpoint handler for cart operations. When a user adds or deletes items from their cart, the relevant methods inside this controller are called. These methods utilize the ```CartHelper``` class to perform various cart operations. The usage of ```CartHelper``` methods will be detailed in the next section.

### 3. TransferGuestCartToLoggedInUserCart.php

- The ```TransferGuestCartToLoggedInUserCart``` class contains the logic to transfer the logged-out user's cart into the logged-in user's cart. By setting the logged-out user cart ID and the logged-in user cart ID, any transformation can occur using this file. This service class can be called from other classes by setting the relevant data members using setter methods.

### 4. CartHelper.php
- The ```CartHelper``` class is the most crucial among the four files. It contains all the business logic related to cart management. Key methods include:
    - ```get()``` - Retrieves cart data. It first attempts to get the data from the cache. If the cache is empty, it fetches the data from the database and sets it in the cache for improved performance in subsequent requests.
    - ```put()```- Adds, updates, or deletes data in the cart. This operation affects both the Redis cache and the PostgreSQL database to ensure data consistency and high performance.

The rest methods are used to do several operations.



## Support

For support, feel free to email on ronaksolanki1310@gmail.com
