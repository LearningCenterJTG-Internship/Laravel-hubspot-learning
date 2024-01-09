import React, { useState } from 'react';
import ReactDOM from 'react-dom/client';


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

    await handleContactUpload();
  };

  const handleContactUpload = async () => {
    try {
      const token = "";
      const response = await fetch('http://127.0.0.1:8001/hubspot/upload-to-contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });

      const responseData = await response.json();

      if (response.ok) {
        console.log('Contact uploaded successfully:', responseData);
      } else {
        console.error('Failed to upload contact:', responseData);
      }
    } catch (error) {
      console.error('Error uploading contact:', error);
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

if (document.getElementById('ContactForm')) {
  const Index = ReactDOM.createRoot(document.getElementById("ContactForm"));

  Index.render(
    <React.StrictMode>
      <ContactForm />
    </React.StrictMode>
  );
}

