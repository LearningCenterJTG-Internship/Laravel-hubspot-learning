// server.js

import express from 'express';
import cors from 'cors';
import fetch from 'node-fetch';

const app = express();
app.use(express.json());
app.use(cors());

app.post('/hubspot/upload-to-contact', async (req, res) => {
  try {
    /*const tokenResponse = await fetch('http://localhost:8000/hubspot/token');
    const tokenData = await tokenResponse.json();

    console.log('Token Response:', tokenData);

    token = tokenData.token;*/

    res.header('Access-Control-Allow-Origin', '*');
    res.header('Content-Type', 'application/json');

    const token = "";

    console.log(req.body);

    const hubspotResponse = await fetch('https://api.hubapi.com/crm/v3/objects/contacts', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify(req.body),
    });

    const hubspotData = await hubspotResponse.json();

    res.json(hubspotData);
  } catch (error) {
    console.error("Not working");
  }
});

const PORT = process.env.PORT || 8001;
app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});

