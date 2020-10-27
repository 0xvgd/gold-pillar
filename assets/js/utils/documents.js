import { upload as fileUpload } from "./files";

export const uploadDocument = (input, onupload) => {
  const filename = input.files[0].name;
  fileUpload(input.files[0], onupload, filename);
};
