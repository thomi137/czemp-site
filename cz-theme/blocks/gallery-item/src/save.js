import {hexToRgba} from "./utils"

export default function Save({ attributes }) {
    const { imageUrl, title, description, overlayColor, overlayOpacity, textAlign, verticalAlign, fontSize } = attributes;

    return (
        <div className="gallery-item">
            {imageUrl && <img src={imageUrl} alt={title} />}
            <div
                className="overlay"
                style={{
                    backgroundColor: hexToRgba(attributes.overlayColor, attributes.overlayOpacity),
                    justifyContent: verticalAlign,
                    alignItems: textAlign,
                    fontSize
                }}
            >
                <h3>{title}</h3>
                <p>{description}</p>
            </div>
        </div>
    );
}