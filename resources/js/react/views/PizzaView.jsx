import React from "react";
import { createRoot } from "react-dom/client";
import Pizza from "../components/Pizza";

const rootElement = document.getElementById("pizzachart");

if (rootElement) {
  const root = createRoot(rootElement);

  // Obtener los datos pasados desde Blade
  const title = JSON.parse(rootElement.dataset.titulo ?? "");
  const format = JSON.parse(rootElement.dataset.format ?? "[]");

  root.render(
    <Pizza
      title={title}
      format={format}
    />
  );
}