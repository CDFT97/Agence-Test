import React from "react";
import { createRoot } from "react-dom/client";
import Barchart from "../components/Barchart";

const rootElement = document.getElementById("barchart");

if (rootElement) {
  const root = createRoot(rootElement);

  // Obtener los datos pasados desde Blade
  const title = JSON.parse(rootElement.dataset.titulo ?? "");
  const format = JSON.parse(rootElement.dataset.format ?? "[]");
  const mesesName = JSON.parse(rootElement.dataset.meses ?? "[]");
  root.render(
    <Barchart
      title={title}
      format={format}
      mesesName={mesesName}
    />
  );
}