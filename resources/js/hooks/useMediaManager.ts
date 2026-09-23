import { useState } from "react";
import { MEDIA_TYPES, RIBBON_ICONS } from "./mediaManager.constants";
import type { RibbonIcon } from "./MediaManager.types";
import { useUploadMedia, useBulkUploadMedia, useDeleteMedia } from "./mediaManager.queries";

type MediaTab = keyof typeof RIBBON_ICONS;

const isMediaTab = (value: string): value is MediaTab => value in RIBBON_ICONS;

const useMediaManager = () => {
  const [selectedTab, setSelectedTab] = useState<MediaTab>(MEDIA_TYPES.IMAGE as MediaTab);
  const [ribbonIcons, setRibbonIcons] = useState<RibbonIcon[]>(
    RIBBON_ICONS[MEDIA_TYPES.IMAGE as MediaTab],
  );
  const [isUploading, setIsUploading] = useState(false);
  const [uploadError, setUploadError] = useState<string | null>(null);

  const uploadMediaMutation = useUploadMedia();
  const bulkUploadMediaMutation = useBulkUploadMedia();
  const deleteMediaMutation = useDeleteMedia();

  const selectTab = (tab: string) => {
    if (!isMediaTab(tab)) return;
    setSelectedTab(tab);
    setRibbonIcons(RIBBON_ICONS[tab]);
  };

  const mapMediaTypeToLowercase = (type: MediaTab): "image" | "video" | "audio" => {
    return type.toLowerCase() as "image" | "video" | "audio";
  };

  const performRibbonAction: Record<string, () => void> = {
    'add': async () => {
      const mediaType = mapMediaTypeToLowercase(selectedTab);
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = getAcceptTypeForMedia(selectedTab);
      
      input.onchange = async (e: Event) => {
        const target = e.target as HTMLInputElement;
        const file = target.files?.[0];
        
        if (!file) return;

        setIsUploading(true);
        setUploadError(null);

        try {
          await uploadMediaMutation({
            variables: {
              input: {
                file,
                mediaType,
              },
            },
          });
        } catch (error) {
          setUploadError(`Failed to upload ${selectedTab}: ${error instanceof Error ? error.message : 'Unknown error'}`);
          console.error(`Error uploading ${selectedTab}:`, error);
        } finally {
          setIsUploading(false);
        }
      };

      input.click();
    },

    'bulk': async () => {
      const mediaType = mapMediaTypeToLowercase(selectedTab);
      const input = document.createElement('input');
      input.type = 'file';
      input.multiple = true;
      input.accept = getAcceptTypeForMedia(selectedTab);
      
      input.onchange = async (e: Event) => {
        const target = e.target as HTMLInputElement;
        const files = Array.from(target.files || []);

        if (files.length === 0) return;

        setIsUploading(true);
        setUploadError(null);

        try {
          await bulkUploadMediaMutation({
            variables: {
              input: {
                files,
                mediaType,
              },
            },
          });
        } catch (error) {
          setUploadError(`Failed to bulk upload ${selectedTab}: ${error instanceof Error ? error.message : 'Unknown error'}`);
          console.error(`Error bulk uploading ${selectedTab}:`, error);
        } finally {
          setIsUploading(false);
        }
      };

      input.click();
    },

    'view': () => {
      switch (selectedTab) {
        case MEDIA_TYPES.IMAGE:
          console.log("Viewing image");
          break;
        case MEDIA_TYPES.VIDEO:
          console.log("Viewing video");
          break;
        case MEDIA_TYPES.AUDIO:
          console.log("Viewing audio");
          break;
        default:
          console.warn("Unknown media type");
      }
    },

    'edit': () => {
      switch (selectedTab) {
        case MEDIA_TYPES.IMAGE:
          console.log("Editing image");
          break;
        case MEDIA_TYPES.VIDEO:
          console.log("Editing video");
          break;
        case MEDIA_TYPES.AUDIO:
          console.log("Editing audio");
          break;
        default:
          console.warn("Unknown media type");
      }
    },

    'delete': async () => {
      const mediaId = ""; // This should be passed from context or state
      if (!mediaId) {
        console.warn("No media selected for deletion");
        return;
      }

      try {
        await deleteMediaMutation({
          variables: { ids: [mediaId] },
        });
      } catch (error) {
        console.error("Error deleting media:", error);
      }
    },

    '': () => {
      console.warn("No action specified");
    },
  };

  return {
    MEDIA_TYPES,
    selectedTab,
    selectTab,
    ribbonIcons,
    performRibbonAction,
    isUploading,
    uploadError,
  };
};

/**
 * Get the appropriate accept attribute value for file inputs based on media type
 */
const getAcceptTypeForMedia = (mediaType: string): string => {
  switch (mediaType) {
    case "IMAGE":
      return "image/*";
    case "VIDEO":
      return "video/*";
    case "AUDIO":
      return "audio/*";
    default:
      return "*";
  }
};

export default useMediaManager;
