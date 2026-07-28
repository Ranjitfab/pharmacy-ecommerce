# RxStock E-Commerce

Welcome to the RxStock project! 

### Just looking around?
If you just want to run the PHP files and see the website, **you don't need to do anything.** The CSS styling is already compiled and ready to go. Just start your XAMPP server and view the site!

---

### Want to edit the design?
This project uses **Tailwind CSS** for styling. If you want to change colors, margins, or add new designs to the PHP files, you need to follow these 3 simple steps:

**Step 1: Install Node.js**
Make sure you have [Node.js](https://nodejs.org/) installed on your computer.

**Step 2: Install Tailwind**
Open your terminal (command prompt), go to this project folder, and type:
```bash
npm install
```
*(This downloads Tailwind onto your computer. It creates a massive `node_modules` folder. Do not push this folder to GitHub!)*

**Step 3: Run the compiler**
To make your design changes show up in the browser, type:
```bash
npm run dev
```
**Leave that terminal open!** As long as it is open, it will watch your code. Every time you hit Save on a `.php` file, it will instantly update the website's styling.

---

### How to change the brand colors:
If you need to change the main green colors, just edit the `src/input.css` file:
```css
@theme {
  --color-primary: #0d5c46;
  --color-primary-hover: #0a4635;
}
```
