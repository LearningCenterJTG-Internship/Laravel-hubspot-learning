import React, { useState } from 'react';

const ContactForm = () => {
  const [formData, setFormData] = useState({
    email: '',
    firstname: '',
    lastname: '',
    phone: '',
    company: '',
    website: '',
    lifecyclestage: '',
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    // Your API endpoint for contact upload
    const apiUrl = 'https://api.hubapi.com/crm/v3/objects/contacts';

    try {
        const tokenResponse = await fetch('http://localhost:8000/hubspot/token');
        const tokenData = await tokenResponse.json();
        const token = "";

        if (tokenResponse.ok) {
            token = tokenData.token;
        }
        
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify(formData),
        });

        if (response.ok) {
            // Handle success, e.g., show a success message
            console.log('Contact uploaded successfully!');
        } else {
            // Handle error, e.g., show an error message
            console.error('Error uploading contact.');
        }
    } catch (error) {
        console.error('Error:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <label htmlFor="email">Email:</label>
      <input
        type="email"
        name="email"
        value={formData.email}
        onChange={handleChange}
        required
      />

      <label htmlFor="firstname">First Name:</label>
      <input
        type="text"
        name="firstname"
        value={formData.firstname}
        onChange={handleChange}
        required
      />

      <label htmlFor="lastname">Last Name:</label>
      <input
        type="text"
        name="lastname"
        value={formData.lastname}
        onChange={handleChange}
        required
      />

      <label htmlFor="phone">Phone:</label>
      <input
        type="tel"
        name="phone"
        value={formData.phone}
        onChange={handleChange}
      />

      <label htmlFor="company">Company:</label>
      <input
        type="text"
        name="company"
        value={formData.company}
        onChange={handleChange}
      />

      <label htmlFor="website">Website:</label>
      <input
        type="url"
        name="website"
        value={formData.website}
        onChange={handleChange}
      />

      <label htmlFor="lifecyclestage">Lifecycle Stage:</label>
      <input
        type="text"
        name="lifecyclestage"
        value={formData.lifecyclestage}
        onChange={handleChange}
      />

      <button type="submit">Upload Contact</button>
    </form>
  );
};

export default ContactForm;

