const express = require('express');
const Wallet = require('cashu-js').Wallet;
const app = express();
const PORT = 3000;

app.use(express.json());

const wallet = new Wallet();

// Endpoint to check if a token is spendable
app.post('/checkSpendable', (req, res) => {
    const token = req.body.token;
    wallet.checkSpendable(token)
        .then(isValid => res.json({ isValid }))
        .catch(err => res.status(500).json({ error: err.message }));
});

// Endpoint to get the total amount of satoshis from a token
app.post('/sumProofs', (req, res) => {
    const token = req.body.token;
    const satoshis = wallet.sumProofs(token);
    res.json({ satoshis });
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
