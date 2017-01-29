<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div class="panel panel-default">
                    <div class="panel-heading">My Cards</div>

                    <div class="panel-body">
                        
                        <div class="alert alert-info" v-if="cards.length == 0">
                            <p>
                                You have no cards stored yet.
                            </p>
                        </div>

                        <ul class="list-group">
                            <li class="list-group-item" v-for="card in cards">
                                <span>
                                    {{ card.number }} 
                                    <span class="pull-right">
                                        Expires: ({{ card.exp_month}}/{{ card.exp_year }})
                                        <label class="label label-primary" v-if="card.id == primary">Primary</label>
                                        <a class="label label-info" v-else @click="setPrimary(card.id)">Make Primary</a>
                                        <a class="label label-danger" @click="removeCard(card)">Delete</a>
                                    </span>
                                </span>
                            </li>
                        </ul>

                    </div>
                </div>

                <div class="panel panel-default" v-if="! readyForThreeDs">
                    <div class="panel-heading">Add New Credit Card</div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="number" class="form-control" placeholder="4242 **** **** 4242" v-model="newcard.number">
                        </div>

                        <div class="form-group">
                            <label>Card Holder</label>
                            <input type="text" name="holder" class="form-control" placeholder="John Doe" v-model="newcard.holder">
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Expiry Month</label>
                                    <input type="text" name="exp_month" class="form-control" placeholder="05" v-model="newcard.exp_month">
                                </div>

                                <div class="col-md-4">
                                    <label>Expiry Year</label>
                                    <input type="text" name="exp_year" class="form-control" placeholder="2017" v-model="newcard.exp_year">
                                </div>

                                <div class="col-md-4">
                                    <label>Card CVV</label>
                                    <input type="text" name="cvv" class="form-control" placeholder="123" v-model="newcard.cvv">
                                </div>
                            </div>                                                        
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" @click.prevent="addCard" :disabled="busy">
                                <span v-if="busy"><i class="fa fa-spinner fa-spin"></i> Saving Card...</span>
                                <span v-else>Save Card</span>
                            </button>
                        </div>

                    </div>
                </div>

                <div class="panel panel-default" v-if="readyForThreeDs">
                    <div class="panel-heading">3DS Secure</div>

                    <div class="panel-body">
                        
                        <div class="alert alert-success">
                            <p>
                                <i class="fa fa-lock"></i> Your Credit Card is enrolled for 3DS Secure validation. In order for us to process your credit card, you are required to verify your ownership. In order to verify your ownership, proceed to Verification
                            </p>
                        </div>

                        <form method="POST" :action="threeDs.url">
                            <input type="hidden" name="connector" :value="threeDs.connector">
                            <input type="hidden" name="MD" :value="threeDs.MD">
                            <input type="hidden" name="TermUrl" :value="threeDs.TermUrl">
                            <input type="hidden" name="PaReq" :value="threeDs.PaReq">
                            <input type="submit" value="Proceed to Verification" class="btn btn-primary">
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            cards: null,
            user: null
        },

        data() {
            return {
                primary: null,
                busy: false,
                readyForThreeDs: false,
                threeDs: {
                    url: '',
                    connector: '',
                    MD: '',
                    TermUrl: '',
                    PaReq: ''
                },
                newcard: {
                    number: '',
                    holder: '',
                    exp_month: '',
                    exp_year: '',
                    cvv: ''
                }
            }
        },

        mounted() {
            this.primary = this.user.primary_card;
        },

        methods: {
            setPrimary(id) {
                this.primary = id;
                axios.post('/account/billing/primary', { id: id });
            },

            removeCard(card) {
                if(card.id == this.primary && this.cards.length > 1) {
                    alert("You cannot remove primary Cards, please add another card before you can remove this card, alternatively set another card as primary.");
                    return;
                } else {
                    this.cards = this.cards.filter(function (c) {
                        return c.id != card.id;
                    });
                }

                axios.post('/account/billing/remove', { card_id: card.id });
            },

            addCard() {
                this.busy = true;
                axios.post('/account/billing', this.newcard)
                    .then(response => {

                        if(response.data.redirect === undefined || response.data.redirect === null) {
                            return window.location.href = "/account/billing/return?id=" + response.data.id;
                        }

                        this.threeDs.url = response.data.redirect.url;
                        for (var i = response.data.redirect.parameters.length - 1; i >= 0; i--) {
                            this.threeDs[response.data.redirect.parameters[i].name] = response.data.redirect.parameters[i].value;
                        }
                        this.readyForThreeDs = true;
                        this.busy = false;
                    })
                    .catch(errors => {
                        console.log(errors);
                        this.busy = false;
                    });
            }
        }
    }
</script>
