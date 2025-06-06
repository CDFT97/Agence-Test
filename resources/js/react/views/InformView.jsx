import React from "react";
import { createRoot } from "react-dom/client";
import Inform from "../components/Inform";

const InformRootElement = document.getElementById("inform");

if (InformRootElement) {
  const root = createRoot(InformRootElement);
  const data = JSON.parse(InformRootElement.dataset.data);
  const titulo = JSON.parse(InformRootElement.dataset.titulo);

  root.render(
    <Inform
      data={data}
      titulo={titulo}
    />
  );
}