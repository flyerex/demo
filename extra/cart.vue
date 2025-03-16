<template>
    <div @change="update" class="content-container mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-content">
                        <div class="commerce">
                            <form>
                                <table class="table shop_table cart">
                                    <thead>
                                    <tr>
                                        <th class="product-remove hidden-xs">&nbsp;</th>
                                        <th class="product-thumbnail hidden-xs">&nbsp;</th>
                                        <th class="product-name">Product</th>
                                        <th class="product-price text-center">Price</th>
                                        <th class="product-quantity text-center">Quantity</th>
                                        <th class="product-subtotal text-center hidden-xs">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="product in getCart.products" class="cart_item">
                                        <td class="product-remove hidden-xs">
                                            <a href="#" @click.prevent="removeItemFromCart(product.prod_id)" class="remove" title="Remove this item">&times;</a>
                                        </td>
                                        <td class="product-thumbnail hidden-xs">
                                            <router-link :to="{name: 'product', params:{id: product.prod_id}}">
                                                <img width="100" height="150" :src="product.img" alt="Product-img"/>
                                            </router-link>
                                        </td>
                                        <td class="product-name">
                                            <router-link :to="{name: 'product', params:{id: product.prod_id}}">{{product.title}}</router-link>
                                            <dl class="variation">
                                                <dt class="variation-Size">Size:</dt>
                                                <dd class="variation-Size"><p>{{product.size}}kg</p></dd>
                                            </dl>
                                        </td>
                                        <td class="product-price text-center">
                                            <span class="amount">&#36;{{product.price}}</span>
                                        </td>
                                        <td class="product-quantity text-center">
                                            <div class="quantity">
                                                <input type="number" step="1" min="0" name="qunatity" @change="changeQty(product.prod_id, $event, 1)" v-model="product.qty" title="Qty" class="input-text qty text" size="4"/>
                                            </div>
                                        </td>
                                        <td class="product-subtotal hidden-xs text-center">
                                            <span class="amount">&#36;{{product.subtotal}}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                            <div class="cart-collaterals">
                                <div class="cart_totals">
                                    <h2>Cart Totals</h2>
                                    <table>
                                        <tr class="cart-subtotal">
                                            <th>Subtotal</th>
                                            <td><span class="amount">&#36;{{getCart.total}}</span></td>
                                        </tr>
                                        <tr class="shipping">
                                            <th>Shipping</th>
                                            <td><span class="amount">&#36;0.00</span></td>
                                        </tr>
                                        <tr class="order-total">
                                            <th>Total</th>
                                            <td><strong><span class="amount">&#36;{{getCart.total}}</span></strong></td>
                                        </tr>
                                    </table>
                                    <div class="wc-proceed-to-checkout">
                                        <router-link to="checkout" class="checkout-button button alt wc-forward rounded">Proceed to Checkout</router-link>
                                    </div>
                                </div>
                                <div class="cross-sells">
                                    <h2>You may be interested in&hellip;</h2>
                                    <ul class="products columns-2">
                                        <li class="product style-2">
                                            <div class="product-container">
                                                <figure>
                                                    <div class="product-wrap">
                                                        <div class="product-images">
                                                            <div class="shop-loop-thumbnail shop-loop-front-thumbnail">
                                                                <a href="shop-detail-1.html"><img width="450" height="450" src="images/products/product_328x328.jpg" alt=""/></a>
                                                            </div>
                                                            <div class="shop-loop-thumbnail shop-loop-back-thumbnail">
                                                                <a href="shop-detail-1.html"><img width="450" height="450" src="images/products/product_328x328alt.jpg" alt=""/></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <figcaption>
                                                        <div class="shop-loop-product-info">
                                                            <div class="info-meta clearfix">
                                                                <div class="star-rating">
                                                                    <span style="width:100%"></span>
                                                                </div>
                                                                <div class="loop-add-to-wishlist">
                                                                    <div class="yith-wcwl-add-to-wishlist">
                                                                        <div class="yith-wcwl-add-button">
                                                                            <a href="#" class="add_to_wishlist">
                                                                                Add to Wishlist
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="info-content-wrap">
                                                                <h3 class="product_title">
                                                                    <a href="shop-detail-1.html">Florence Knoll Credenza</a>
                                                                </h3>
                                                                <div class="info-price">
																			<span class="price">
																				<span class="amount">£17.50</span>
																			</span>
                                                                </div>
                                                                <div class="loop-action">
                                                                    <div class="loop-add-to-cart">
                                                                        <a href="#" class="add_to_cart_button">
                                                                            Add to cart
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </figcaption>
                                                </figure>
                                            </div>
                                        </li>
                                        <li class="product style-2">
                                            <div class="product-container">
                                                <figure>
                                                    <div class="product-wrap">
                                                        <div class="product-images">
                                                            <div class="shop-loop-thumbnail shop-loop-front-thumbnail">
                                                                <a href="shop-detail-1.html"><img width="450" height="450" src="images/products/product_328x328.jpg" alt=""/></a>
                                                            </div>
                                                            <div class="shop-loop-thumbnail shop-loop-back-thumbnail">
                                                                <a href="shop-detail-1.html"><img width="450" height="450" src="images/products/product_328x328alt.jpg" alt=""/></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <figcaption>
                                                        <div class="shop-loop-product-info">
                                                            <div class="info-meta clearfix">
                                                                <div class="star-rating">
                                                                    <span style="width:100%"></span>
                                                                </div>
                                                                <div class="loop-add-to-wishlist">
                                                                    <div class="yith-wcwl-add-to-wishlist">
                                                                        <div class="yith-wcwl-add-button">
                                                                            <a href="#" class="add_to_wishlist">
                                                                                Add to Wishlist
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="info-content-wrap">
                                                                <h3 class="product_title">
                                                                    <a href="shop-detail-1.html">Citterio Grand Repos</a>
                                                                </h3>
                                                                <div class="info-price">
																			<span class="price">
																				<span class="amount">£12.00</span>
																				–
																				<span class="amount">£20.00</span>
																			</span>
                                                                </div>
                                                                <div class="loop-action">
                                                                    <div class="loop-add-to-cart">
                                                                        <a href="#" class="add_to_cart_button">
                                                                            Add to cart
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </figcaption>
                                                </figure>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>
<script>
    import {mapGetters, mapActions} from 'vuex'
    export default {
        name: 'Cart',

        computed:{
            ...mapGetters(['getCart'])
        },

        mounted() {
            this.update()
        },

        methods:{
            changeQty(id, e, update){
                this.$store.dispatch('addToCart', {id: id, qty: e.target.value, update:update})
            },

            update(){
                this.$store.dispatch('getCart')
            },

            removeItemFromCart(id){
                this.$store.dispatch('removeFromCart', {id: id})
                this.update()
            }
        },
    }
</script>
