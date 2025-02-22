#### Use Case Specification

##### Index Page
- Anyone can see the index page.
- Nav Bar on top with products on sale displayed.

##### Register Account
- Guest must enter their name, date of birth, email address, and a password to create an account.
- User selects if they are a buyer or seller.
- System verifies if the account is unique.
- System stores details in the database (name, email, password, etc.).
- System confirms account is created.

##### Login
- User must enter their username (may or may not be email) and password to log in.
- System makes an authentication request to the database to verify the credentials.
- Authentication is successful, and the system grants access to the user's account.
- User is redirected to the home page.

##### Browse & Search
- User browses for products. Can use a broad filter.
- Can use the search bar for product name.

##### Product Description
- User can click on any item to view product details, images, and sales information.
- User can add the item to their cart or wishlist.

##### Create/Modify Listing (Sellers)
- Seller logs into their account to create a new listing.
- Seller provides item details (title, description, images, price).
- Seller selects whether the item is part of an auction or fixed-price sale.
- System stores the listing in the database.
- Listing becomes available for buyers to view.

##### Wishlist
- User can add products to their wishlist by clicking the "Add to Wishlist" button.
- Wishlist is stored in the database for the user.
- User can view items from the wishlist at any time.
- System allows users to move items from wishlist to cart when ready to purchase.

##### Cart
- User can add items to their cart with an option to modify quantities or remove items.
- System calculates the total price of the items in the cart and shows the summary.
- User proceeds to checkout and confirms their order.

##### Order Summary
- User is shown a summary of their order.
- Redirected to Index page.
